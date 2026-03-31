<?php

/**
 * Route: /user/sessions/book/return
 *
 * PayHere redirects here after a completed payment.
 * On success (status_code = 2):
 *   1. Confirm booking hold
 *   2. Generate Google Meet link
 *   3. Create session record (with Meet link)
 *   4. Record transaction
 *   5. Redirect to booking success page
 */

require_once __DIR__ . '/../../../common/user.head.php';
require_once __DIR__ . '/../book.model.php';
require_once __DIR__ . '/../../../../../core/GoogleMeetService.php';

// ------------------------------------------------------------------
// 1. Read params
// ------------------------------------------------------------------
$holdId         = (int)(Request::get('holdId') ?? 0);
$payhereOrderId = trim((string)(Request::get('order_id')       ?? ''));
$payherePayId   = trim((string)(Request::get('payment_id')     ?? ''));
$statusCode     = trim((string)(Request::get('status_code')    ?? ''));
$amount         = trim((string)(Request::get('payhere_amount') ?? '0'));
$currency       = trim((string)(Request::get('payhere_currency') ?? 'LKR'));

function redirectWithError(string $msg): never
{
    Response::redirect('/user/sessions?error=' . urlencode($msg));
    exit;
}

// ------------------------------------------------------------------
// 2. Validate hold
// ------------------------------------------------------------------
if ($holdId <= 0) {
    redirectWithError('Invalid booking reference.');
}

$hold = BookingModel::getHold($holdId);
if (!$hold) {
    redirectWithError('Booking hold not found.');
}
if ($hold['status'] === 'confirmed') {
    // Already processed (duplicate redirect) — send to sessions list
    Response::redirect('/user/sessions?booked=1');
    exit;
}
if ($hold['status'] !== 'held') {
    redirectWithError('Your slot reservation has expired. Please try again.');
}

// ------------------------------------------------------------------
// 3. Accept the payment — status_code 2 = success
// ------------------------------------------------------------------
if ($statusCode !== '2') {
    BookingModel::releaseHold($holdId);
    redirectWithError('Payment was not successful. Please try again.');
}

// ------------------------------------------------------------------
// 4. Generate Google Meet link (best-effort — session is created even
//    if Meet link generation fails; the link can be added later)
// ------------------------------------------------------------------
$userId      = (int)$user['id'];
$counselorId = $hold['counselorId'];
$slotDt      = $hold['slotDatetime'];
$durationMin = $hold['durationMinutes'];

$counselorInfo = BookingModel::getCounselorForBooking($counselorId);
$counselorName = $counselorInfo ? $counselorInfo['name'] : 'Your Counselor';

$meetLink = GoogleMeetService::createMeetLink(
    title:          'New Path Counseling Session with ' . $counselorName,
    startDatetime:  $slotDt,
    durationMin:    $durationMin,
    timeZone:       env('APP_TIMEZONE', 'Asia/Colombo'),
    description:    'Online counseling session booked via New Path.',
    counselorEmail: $counselorInfo['email'] ?? null
);

// ------------------------------------------------------------------
// 5. Create session record
// ------------------------------------------------------------------
$sessionId = BookingModel::createSession(
    $userId,
    $counselorId,
    $slotDt,
    $durationMin,
    'video',
    $meetLink ?? ''
);

if ($sessionId <= 0) {
    BookingModel::releaseHold($holdId);
    redirectWithError('Failed to create your session. Please contact support.');
}

// ------------------------------------------------------------------
// 6. Record transaction
// ------------------------------------------------------------------
$fee = (float)$amount;
if ($fee <= 0) {
    $fee = $counselorInfo ? (float)$counselorInfo['fee'] : 0;
}

BookingModel::createTransaction(
    $sessionId,
    $userId,
    $counselorId,
    $fee,
    $payhereOrderId,
    $payherePayId,
    $statusCode
);

// ------------------------------------------------------------------
// 7. Confirm hold and redirect to success page
// ------------------------------------------------------------------
BookingModel::confirmHold($holdId);

Response::redirect('/user/sessions/book/success?session_id=' . $sessionId);
exit;
