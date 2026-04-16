<?php
require_once __DIR__ . '/../../common/user.head.php';
require_once __DIR__ . '/../recovery.model.php';

if (!Request::isPost()) {
    Response::redirect('/user/recovery/browse');
}

$planId  = (int)(Request::post('planId') ?? 0);
$adopted = false;

if ($planId > 0) {
    $adopted = RecoveryModel::adoptSystemPlan($planId, (int)$user['id']);
    if (!$adopted) {
        Response::redirect('/user/recovery/browse?error=already_active');
        exit;
    }
}

Response::redirect('/user/recovery/manage');
