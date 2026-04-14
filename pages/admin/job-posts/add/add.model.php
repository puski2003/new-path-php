<?php
require_once __DIR__ . '/../model.php';

class AddJobPostModel
{
    public static function create(array $input, int $adminUserId): bool
    {
        return JobPostsModel::createJobPost($input, $adminUserId);
    }
}
