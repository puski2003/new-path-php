<?php
require_once __DIR__ . '/../../common/user.head.php';
require_once __DIR__ . '/../post-recovery.model.php';

header('Content-Type: application/json');

if (!Request::isPost()) {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);
$jobId = (int)($payload['jobId'] ?? 0);

if ($jobId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid job id']);
    exit;
}

$saved = PostRecoveryModel::toggleSaveJob((int)$user['id'], $jobId);
echo json_encode(['success' => true, 'saved' => $saved]);
exit;

