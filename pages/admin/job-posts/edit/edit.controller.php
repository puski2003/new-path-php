<?php
require_once __DIR__ . '/edit.model.php';

$jobId = (int) (Request::get('id') ?? Request::post('jobId') ?? 0);
$jobPost = EditJobPostModel::getJobPost($jobId);
$error = '';
$success = '';

if (!$jobPost) {
    $error = 'Job post not found.';
} elseif (Request::isPost()) {
    if (EditJobPostModel::update($jobId, $_POST)) {
        $success = 'Job post updated successfully.';
        $jobPost = EditJobPostModel::getJobPost($jobId);
    }
}
