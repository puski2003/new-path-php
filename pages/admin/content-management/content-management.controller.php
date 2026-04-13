<?php
require_once __DIR__ . '/content-management.model.php';
$filters = ['type' => Request::get('type') ?? 'all', 'reason' => Request::get('reason') ?? 'all', 'status' => Request::get('status') ?? 'all'];
$reportedContent = ContentManagementModel::getReports($filters);
$contentCount = count($reportedContent);
$reportedContentPagination = [
    'currentPage' => 1,
    'totalPages' => 1,
    'totalRows' => $contentCount,
    'fromRow' => 1,
    'toRow' => $contentCount,
    'offset' => 0,
    'perPage' => 15,
];
$totalReportsToday = $contentCount;
$pendingReports = count(array_filter($reportedContent, static fn($item) => $item['status'] === 'pending'));
$actionsThisWeek = 156;
$activeBans = 8;
