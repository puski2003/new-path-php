<?php
require_once __DIR__ . '/../common/admin.data.php';

class CounselorManagementModel
{
    public static function getPageData(array $filters): array
    {
        return [
            'stats' => AdminData::getCounselorStats(),
            'applications' => AdminData::getCounselorApplications($filters['appStatus']),
            'counselors' => AdminData::getCounselors($filters),
        ];
    }
}
