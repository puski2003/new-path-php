<?php
$plans = CounselorRecoveryPlansModel::getAll((int) ($user['counselorId'] ?? 0));
$query=trim(Request::get('q') ?? '');
if(!empty($query)){
    $plans=CounselorRecoveryPlansModel::searchPlans($query,(int)($user['counselorId']));
}
