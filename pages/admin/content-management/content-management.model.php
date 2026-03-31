<?php
require_once __DIR__ . '/../common/admin.data.php';

class ContentManagementModel
{
    public static function getReports(array $filters): array
    {
        return AdminData::getReportedContent($filters);
    }
}
