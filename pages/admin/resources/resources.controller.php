<?php
require_once __DIR__ . '/resources.model.php';

$activeTab = Request::get('tab') ?? 'job-ads';
$filters = [
    'status' => Request::get('status') ?? 'all',
    'location' => Request::get('location') ?? 'all',
    'jobType' => Request::get('jobType') ?? 'all',
    'centerStatus' => Request::get('centerStatus') ?? 'all',
    'type' => Request::get('type') ?? 'all',
    'centerCategory' => Request::get('centerCategory') ?? 'all',
];

$page = Pagination::sanitizePage(Request::get('page') ?? 1);
$perPage = 15;

$data = ResourcesModel::getData($activeTab, $filters, $page, $perPage);
$jobPosts = $data['jobPosts']['items'];
$jobPostsPagination = $data['jobPosts']['pagination'];
$helpCenters = $data['helpCenters']['items'];
$helpCentersPagination = $data['helpCenters']['pagination'];
$programs = $data['programs'];
$programsPagination = $data['programsPagination'];
$pendingPrograms = $data['pendingPrograms'];
$pendingProgramsPagination = $data['pendingProgramsPagination'];
