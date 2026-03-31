<?php

/**
 * User Dashboard Controller
 * GET only — no forms on this page (quick-log POST is a separate route).
 * Loads data from the model and prepares $data for the layout.
 */
require_once __DIR__ . '/dashboard.model.php';

$userId = (int) $user['id'];

// Core metrics
$daysSober         = UserDashboardModel::getDaysSober($userId);
$milestoneData     = UserDashboardModel::getMilestoneProgress($daysSober);
$milestoneProgress = $milestoneData['progress'];
$nextMilestone     = $milestoneData['nextMilestone'];

// Sections
$nextSession         = UserDashboardModel::getNextSession($userId);
$communityHighlights = UserDashboardModel::getCommunityHighlights(3);
$dailyTasks          = UserDashboardModel::getDailyTasks($userId, 5);
$progressPercentage  = UserDashboardModel::getProgressPercentage($userId);
$achievements        = UserDashboardModel::getAchievements($daysSober);

// User display name
$userName = $user['name'] ?? 'User';

$data = compact(
    'daysSober',
    'milestoneProgress',
    'nextMilestone',
    'nextSession',
    'communityHighlights',
    'dailyTasks',
    'progressPercentage',
    'achievements',
    'userName'
);
