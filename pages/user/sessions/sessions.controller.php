<?php

$userId = (int)$user['id'];

$upcomingPage = filter_var(Request::get('upage'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;
$historyPage = filter_var(Request::get('hpage'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;
$activeTab = Request::get('tab') === 'history' ? 'history' : 'upcoming';

$upcomingData = SessionsModel::getSessionsByType($userId, 'upcoming', $upcomingPage, 5);
$historyData = SessionsModel::getSessionsByType($userId, 'history', $historyPage, 5);

$upcomingSessions = $upcomingData['items'];
$historySessions = $historyData['items'];

$upcomingCurrentPage = (int)$upcomingData['page'];
$upcomingTotalPages = (int)$upcomingData['totalPages'];
$upcomingTotal = (int)$upcomingData['total'];
$historyCurrentPage = (int)$historyData['page'];
$historyTotalPages = (int)$historyData['totalPages'];
$historyTotal = (int)$historyData['total'];

$pageTitle = 'Sessions';
$pageStyle = ['user/dashboard', 'user/sessions'];
