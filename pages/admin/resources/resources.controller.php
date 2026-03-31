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

$data = ResourcesModel::getData($activeTab, $filters);
$jobPosts = $data['jobPosts'];
$helpCenters = $data['helpCenters'];
$programs = $data['programs'];
$pendingPrograms = $data['pendingPrograms'];
