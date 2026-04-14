<?php
require_once __DIR__ . '/sessions.model.php';

// -- AJAX: return meeting details JSON for the admin modal --
if (Request::isAjax() && Request::get('action') === 'meeting_details') {
    $sessionId = (int)(Request::get('session_id') ?? 0);
    if ($sessionId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid session ID']);
        exit;
    }

    $details = AdminSessionsModel::getMeetingDetails($sessionId);
    if (!$details) {
        echo json_encode(['error' => 'No meeting data available for this session.']);
        exit;
    }

    echo json_encode($details);
    exit;
}

// -- Standard page load --
$search  = trim((string)(Request::get('search') ?? ''));
$page    = max(1, (int)(Request::get('page') ?? 1));
$limit   = 20;
$offset  = ($page - 1) * $limit;

$result     = AdminSessionsModel::getSessions($search, $limit, $offset);
$sessions   = $result['rows'];
$totalCount = $result['total'];
$totalPages = (int)ceil($totalCount / $limit);
