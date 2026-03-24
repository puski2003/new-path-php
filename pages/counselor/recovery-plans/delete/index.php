<?php

require_once __DIR__ . '/../../common/counselor.head.php';
require_once __DIR__ . '/../../common/counselor.data.php';

$planId = (int) (Request::get('planId') ?? 0);
if ($planId > 0) {
    CounselorData::deletePlan((int) ($user['counselorId'] ?? 0), $planId);
}

Response::redirect('/counselor/recovery-plans');
