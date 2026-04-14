<?php
require_once __DIR__ . '/../../common/admin.head.php';
require_once __DIR__ . '/../model.php';

if (Request::isPost()) {
    $jobId = (int) (Request::post('jobId') ?? 0);
    if ($jobId > 0) {
        JobPostsModel::deleteJobPost($jobId);
    }
}
Response::redirect('/admin/resources?tab=job-ads');
