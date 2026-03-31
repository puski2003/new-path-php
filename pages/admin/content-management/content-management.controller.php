<?php
require_once __DIR__ . '/content-management.model.php';
$filters = ['type' => Request::get('type') ?? 'all', 'reason' => Request::get('reason') ?? 'all', 'status' => Request::get('status') ?? 'all'];
$reportedContent = ContentManagementModel::getReports($filters);
$totalReportsToday = count($reportedContent);
$pendingReports = count(array_filter($reportedContent, static fn($item) => $item['status'] === 'pending'));
$actionsThisWeek = 156;
$activeBans = 8;
