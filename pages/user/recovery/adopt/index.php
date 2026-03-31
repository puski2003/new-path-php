<?php
require_once __DIR__ . '/../../common/user.head.php';
require_once __DIR__ . '/../recovery.model.php';

if (!Request::isPost()) {
    Response::redirect('/user/recovery/browse');
}

$planId = (int)(Request::post('planId') ?? 0);
if ($planId > 0) {
    RecoveryModel::adoptGeneralPlan($planId, (int)$user['id']);
}

Response::redirect('/user/recovery');
