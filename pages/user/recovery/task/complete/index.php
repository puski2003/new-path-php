<?php
require_once __DIR__ . '/../../../common/user.head.php';
require_once __DIR__ . '/../../recovery.model.php';

if (!Request::isPost()) {
    Response::redirect('/user/recovery');
}

$taskId = (int)(Request::post('taskId') ?? 0);
if ($taskId > 0) {
    RecoveryModel::completeTask($taskId, (int)$user['id']);
}

Response::redirect('/user/recovery?taskCompleted=1');
