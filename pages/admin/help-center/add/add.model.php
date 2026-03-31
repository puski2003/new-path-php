<?php
require_once __DIR__ . '/../../common/admin.data.php';

class AddHelpCenterModel
{
    public static function create(array $input, int $adminUserId): bool
    {
        return AdminData::createHelpCenter($input, $adminUserId);
    }
}
