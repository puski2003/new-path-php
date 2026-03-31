<?php
require_once __DIR__ . '/../common/admin.data.php';

class RecoveryPlansAdminModel
{
    public static function getTemplates(array $filters): array
    {
        return AdminData::getRecoveryTemplates($filters);
    }

    public static function getQuestions(array $filters): array
    {
        return AdminData::getOnboardingQuestions($filters);
    }
}
