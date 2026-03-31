<?php
require_once __DIR__ . '/../common/admin.data.php';

class FinancesModel
{
    public static function getSummary(): array
    {
        return AdminData::getFinanceSummary();
    }

    public static function getDisputes(array $filters): array
    {
        return AdminData::getRefundDisputes($filters);
    }

    public static function getTransactions(string $search): array
    {
        return AdminData::getTransactions($search);
    }
}
