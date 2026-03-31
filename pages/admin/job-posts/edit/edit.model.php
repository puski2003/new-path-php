<?php
require_once __DIR__ . '/../../common/admin.data.php';

class EditJobPostModel
{
    public static function getJobPost(int $jobId): ?array
    {
        return AdminData::getJobPostById($jobId);
    }

    public static function update(int $jobId, array $input): bool
    {
        return AdminData::updateJobPost($jobId, $input);
    }
}
