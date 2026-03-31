<?php
require_once __DIR__ . '/../common/admin.data.php';

class UserManagementModel
{
    public static function getUsers(array $filters): array
    {
        return AdminData::getUsers($filters);
    }
}
