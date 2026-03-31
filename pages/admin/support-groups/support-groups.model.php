<?php
require_once __DIR__ . '/../common/admin.data.php';

class SupportGroupsModel
{
    public static function getGroups(array $filters): array
    {
        return AdminData::getSupportGroups($filters);
    }
}
