<?php
require_once __DIR__ . '/counselor-management.model.php';

if (Request::isPost()) {
    header('Content-Type: application/json');
    $applicationId = (int) (Request::post('applicationId') ?? 0);
    $action = Request::post('action') ?? '';

    if ($applicationId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Application ID required.']);
        exit;
    }

    if ($action === 'approve') {
        $result = CounselorManagementModel::approveCounselorApplication($applicationId, (int) $user['id']);
    } elseif ($action === 'reject') {
        $result = CounselorManagementModel::rejectCounselorApplication($applicationId, (int) $user['id'], Request::post('notes') ?? '');
    } else {
        $result = ['ok' => false, 'message' => 'Invalid action.'];
    }

    echo json_encode(['success' => $result['ok'], 'message' => $result['message']]);
    exit;
}

$filters = [
    'appStatus' => Request::get('appStatus') ?? 'pending',
    'specialization' => Request::get('specialization') ?? 'all',
    'counselorStatus' => Request::get('counselorStatus') ?? 'all',
];

$pageData = CounselorManagementModel::getPageData($filters);
$stats = $pageData['stats'];
$applications = $pageData['applications'];
$counselors = $pageData['counselors'];
