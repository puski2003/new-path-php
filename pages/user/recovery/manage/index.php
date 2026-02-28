<?php
require_once __DIR__ . '/../../common/user.head.php';
require_once __DIR__ . '/../recovery.model.php';

$userId = (int)$user['id'];
$activePlans = RecoveryModel::getUserActivePlans($userId);
if (empty($activePlans)) {
    Response::redirect('/user/recovery/browse');
}

$planId = (int)$activePlans[0]['planId'];
Response::redirect('/user/recovery/view?planId=' . $planId);
