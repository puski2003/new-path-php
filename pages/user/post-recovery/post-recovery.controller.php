<?php

$userId = (int)$user['id'];
$searchQuery = trim((string)(Request::get('q') ?? ''));
$onlySaved = ((string)(Request::get('my') ?? '0')) === '1';
$currentPage = filter_var(Request::get('page'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;

$jobsData = PostRecoveryModel::getJobs($userId, [
    'q' => $searchQuery,
    'onlySaved' => $onlySaved,
    'page' => $currentPage,
    'perPage' => 8,
]);

$jobPosts = $jobsData['items'];
$totalJobs = (int)$jobsData['total'];
$totalPages = (int)$jobsData['totalPages'];
$currentPage = (int)$jobsData['page'];

$pageTitle = 'Post Recovery';
$pageStyle = ['user/dashboard', 'user/post-recovery'];

