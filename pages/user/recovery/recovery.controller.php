<?php

$userId = (int)$user['id'];

$tasks = RecoveryModel::getUserDailyTasks($userId);
$taskStats = RecoveryModel::getUserTaskStats($userId);
$activePlans = RecoveryModel::getUserActivePlans($userId);
$pendingPlans = RecoveryModel::getAssignedPlansForUser($userId);

$shortTermGoal = null;
$longTermGoal = null;
$progressPercentage = 0;

if (!empty($activePlans)) {
    $activePlan = $activePlans[0];
    $progressPercentage = (int)($activePlan['progressPercentage'] ?? 0);

    $goals = RecoveryModel::getGoalsByPlanId((int)$activePlan['planId']);
    foreach ($goals as $goal) {
        if (($goal['goalType'] ?? '') === 'short_term' && $shortTermGoal === null) {
            $shortTermGoal = $goal;
        } elseif (($goal['goalType'] ?? '') === 'long_term' && $longTermGoal === null) {
            $longTermGoal = $goal;
        }
    }
}

$progressStats = RecoveryModel::getProgressStats($userId);
$daysSober = (int)$progressStats['daysSober'];
$totalDaysTracked = (int)$progressStats['totalDaysTracked'];
$urgesLogged = (int)$progressStats['urgesLogged'];
$sessionsCompleted = (int)$progressStats['sessionsCompleted'];

$progressCirclePercentage = min(100, (int)(($daysSober * 100) / 100));
$strokeOffset = number_format(282.7 - (282.7 * $progressCirclePercentage / 100), 1, '.', '');

$completedCount = (int)$taskStats['completed'];
$pendingCount = (int)$taskStats['pending'];

$nextSession = RecoveryModel::getNextSessionSummary($userId);
$nextSessionTime = $nextSession['time'];
$counselorName = $nextSession['counselorName'];
$counselorNotes = "Great progress this week! Let's focus on mindfulness techniques next session.";

$pageTitle = 'Recovery Plan';
$pageStyle = ['user/dashboard', 'user/recovery'];
