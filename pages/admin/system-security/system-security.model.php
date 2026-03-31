<?php
require_once __DIR__ . '/../common/admin.data.php';

class SystemSecurityModel
{
    public static function getLogs(array $filters): array
    {
        return AdminData::getAuditLogs($filters);
    }
}
