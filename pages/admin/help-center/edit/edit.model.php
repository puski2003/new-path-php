<?php
require_once __DIR__ . '/../../common/admin.data.php';

class EditHelpCenterModel
{
    public static function getCenter(int $helpCenterId): ?array
    {
        return AdminData::getHelpCenterById($helpCenterId);
    }

    public static function update(int $helpCenterId, array $input): bool
    {
        return AdminData::updateHelpCenter($helpCenterId, $input);
    }
}
