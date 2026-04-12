<?php
require_once __DIR__ . '/counselor-management.model.php';

$filters = [
    'appStatus' => Request::get('appStatus') ?? 'pending',
    'specialization' => Request::get('specialization') ?? 'all',
    'counselorStatus' => Request::get('counselorStatus') ?? 'all',
];

$pageData = CounselorManagementModel::getPageData($filters);
$stats = $pageData['stats'];
$applications = $pageData['applications'];
$counselors = $pageData['counselors'];
