<?php

/**
 * POST /counselor/profile/update
 * Updates counselor profile — users table + counselors table.
 */
require_once ROOT . '/pages/counselor/common/counselor.head.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::redirect('/counselor/dashboard');
    exit;
}

$uid         = (int) $user['id'];
$counselorId = (int) $user['counselorId'];

// Sanitise helpers
$db = Database::$connection;
function esc($val) { global $db; return $db->real_escape_string(trim($val)); }

$displayName     = esc($_POST['displayName']      ?? '');
$title           = esc($_POST['title']            ?? '');
$specialty       = esc($_POST['specialty']        ?? '');
$bio             = esc($_POST['bio']              ?? '');
$email           = esc($_POST['email']            ?? '');
$phone           = esc($_POST['phoneNumber']      ?? '');
$fee             = !empty($_POST['consultationFee']) ? (float) $_POST['consultationFee'] : 0;

// Build availability JSON from day checkboxes + time selects
$days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
$availability = [];
foreach ($days as $day) {
    if (!empty($_POST["{$day}_enabled"])) {
        $availability[$day] = [
            'start' => $_POST["{$day}_start"] ?? '09:00',
            'end'   => $_POST["{$day}_end"]   ?? '17:00',
        ];
    }
}
$availJson = esc(json_encode($availability));

// Handle profile picture upload
$picUpdate = '';
$uploadDir = ROOT . '/public/uploads/profiles';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
    $ext         = pathinfo($_FILES['profilePicture']['name'], PATHINFO_EXTENSION);
    $newFile     = 'profile_' . $uid . '_' . time() . '.' . $ext;
    $dest        = $uploadDir . '/' . $newFile;
    if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $dest)) {
        $picUrl    = esc('/uploads/profiles/' . $newFile);
        $picUpdate = ", profile_picture = '$picUrl'";
    }
}

// Update users table
Database::iud(
    "UPDATE users
     SET display_name = '$displayName', email = '$email', phone_number = '$phone' $picUpdate
     WHERE user_id = $uid"
);

// Update counselors table
Database::iud(
    "UPDATE counselors
     SET title = '$title', specialty = '$specialty', bio = '$bio',
         consultation_fee = $fee, availability_schedule = '$availJson'
     WHERE counselor_id = $counselorId"
);

// Refresh JWT with updated name/picture
$newPayload               = $user;
$newPayload['name']       = $displayName ?: $user['name'];
if ($picUpdate !== '') $newPayload['profilePictureUrl'] = '/uploads/profiles/' . $newFile;

Auth::setTokenCookie(Auth::sign($newPayload));

Response::redirect('/counselor/dashboard?updateSuccess=1');
