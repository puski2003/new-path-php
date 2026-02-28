<?php

$sessionIdRaw = Request::get('id');
$sessionId = filter_var($sessionIdRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($sessionId === false) {
    Response::status(404);
    require ROOT . '/pages/404.php';
    exit;
}

$sessionData = SessionsModel::getSessionById((int)$user['id'], (int)$sessionId);
if ($sessionData === null) {
    Response::status(404);
    require ROOT . '/pages/404.php';
    exit;
}

$isUpcomingSession = in_array($sessionData['status'], ['scheduled', 'confirmed', 'in_progress'], true)
    && strtotime((string)$sessionData['sessionDateTime']) >= time();

$pageTitle = 'Session Details';
$pageStyle = ['user/dashboard', 'user/sessions'];

