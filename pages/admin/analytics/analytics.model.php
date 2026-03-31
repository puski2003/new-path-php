<?php
require_once __DIR__ . '/../common/admin.data.php';

class AnalyticsModel
{
    public static function getSummary(): array
    {
        return AdminData::getAnalyticsSummary();
    }
}
