<?php
require_once __DIR__ . '/../common/admin.data.php';

class SettingsModel
{
    public static function getSettings(): array
    {
        return AdminData::getSystemSettings();
    }

    public static function save(array $input, int $adminUserId): bool
    {
        return AdminData::saveSystemSettings($input, $adminUserId);
    }

    public static function getRoles(): array
    {
        return AdminData::getAdminRoles();
    }
}
