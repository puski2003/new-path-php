<?php
require_once __DIR__ . '/../common/admin.data.php';

class UserManagementModel
{
    public static function getUsers(array $filters): array
    {
        return AdminData::getUsers($filters);
    }

    public static function getUsersPaginated(array $filters, int $page = 1, int $perPage = 15): array
    {
        return AdminData::getUsersPaginated($filters, $page, $perPage);
    }

    public static function getUserById(int $userId): ?array
    {
        return AdminData::getUserById($userId);
    }

    public static function updateUser(int $userId, array $input): array
    {
        return AdminData::updateUser($userId, $input);
    }

    public static function deleteUser(int $userId, int $actorUserId): array
    {
        return AdminData::deleteUser($userId, $actorUserId);
    }
}