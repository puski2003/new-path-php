<?php
require_once __DIR__ . '/../../../common/user.head.php';
require_once __DIR__ . '/../../recovery.model.php';

if (!Request::isPost()) {
    Response::redirect('/user/recovery');
}

$taskId   = (int)(Request::post('taskId') ?? 0);
$returnTo = Request::post('returnTo') ?? '';

$result = false;
if ($taskId > 0) {
    $result = RecoveryModel::completeTask($taskId, (int)$user['id']);
}

if ($returnTo === 'dashboard') {
    Response::redirect($result ? '/user/dashboard?taskCompleted=1' : '/user/dashboard?taskBlocked=1');
} else {
    Response::redirect($result ? '/user/recovery?taskCompleted=1' : '/user/recovery?taskBlocked=1');
}
