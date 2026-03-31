<?php
require_once __DIR__ . '/../../common/admin.head.php';
if (Request::isPost()) {
    require_once __DIR__ . '/../../common/admin.data.php';
    $jobId = (int) (Request::post('jobId') ?? 0);
    if ($jobId > 0) {
        AdminData::deleteJobPost($jobId);
    }
}
Response::redirect('/admin/resources?tab=job-ads');
