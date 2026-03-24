<?php

require_once __DIR__ . '/dashboard.model.php';

$counselorId = (int) ($user['counselorId'] ?? 0);
$currentCounselor = CounselorDashboardModel::getCounselorById($counselorId) ?? $currentCounselor;
$upcomingSessions = CounselorDashboardModel::getUpcomingSessionsByCounselor($counselorId);
$activeClients = CounselorDashboardModel::getActiveClientsCount($counselorId);
$totalIncome = count($upcomingSessions) * 50;
$clientActivities = CounselorDashboardModel::getClientActivities($counselorId);

$data = compact(
    'currentCounselor',
    'upcomingSessions',
    'activeClients',
    'totalIncome',
    'clientActivities'
);
