<?php
require_once __DIR__ . '/../../common/admin.data.php';

class AddJobPostModel
{
    public static function create(array $input, int $adminUserId): bool
    {
        return AdminData::createJobPost($input, $adminUserId);
    }
}
