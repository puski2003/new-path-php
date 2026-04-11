<?php
require_once __DIR__ . '/../model.php';

class EditJobPostModel
{
    public static function getJobPost(int $jobId): ?array
    {
        return JobPostsModel::getJobPostById($jobId);
    }

    public static function update(int $jobId, array $input): bool
    {
        return JobPostsModel::updateJobPost($jobId, $input);
    }
}
