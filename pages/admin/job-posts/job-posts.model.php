<?php

class JobPostsModel
{
    private static function esc(string $value): string
    {
        Database::setUpConnection();
        return Database::$connection->real_escape_string($value);
    }

    private static function normalizeJobType(string $jobType): string
    {
        $normalized = strtolower(trim($jobType));
        return match ($normalized) {
            'full-time', 'full_time' => 'full_time',
            'part-time', 'part_time' => 'part_time',
            'contract' => 'contract',
            'temporary' => 'temporary',
            'internship' => 'internship',
            default => 'full_time',
        };
    }

    public static function getJobPosts(array $filters = []): array
    {
        $where = ['1=1'];
        $status = trim((string) ($filters['status'] ?? 'all'));
        if ($status === 'active' || $status === 'approved') {
            $where[] = 'jp.is_active = 1';
        } elseif ($status === 'inactive' || $status === 'rejected') {
            $where[] = 'jp.is_active = 0';
        }

        $location = trim((string) ($filters['location'] ?? 'all'));
        if ($location !== '' && $location !== 'all') {
            $where[] = "jp.location = '" . self::esc($location) . "'";
        }

        $jobType = trim((string) ($filters['jobType'] ?? 'all'));
        if ($jobType !== '' && $jobType !== 'all') {
            $where[] = "jp.job_type = '" . self::normalizeJobType($jobType) . "'";
        }

        $rs = Database::search(
            "SELECT jp.*, a.full_name AS admin_name
             FROM job_posts jp
             LEFT JOIN admin a ON a.admin_id = jp.created_by
             WHERE " . implode(' AND ', $where) . "
             ORDER BY jp.created_at DESC, jp.job_id DESC"
        );

        $items = [];
        while ($rs && ($row = $rs->fetch_assoc())) {
            $items[] = [
                'jobId' => (int) $row['job_id'],
                'title' => $row['title'] ?? '',
                'company' => $row['company'] ?? '',
                'description' => $row['description'] ?? '',
                'requirements' => $row['requirements'] ?? '',
                'location' => $row['location'] ?? '',
                'jobType' => ucwords(str_replace('_', '-', (string) ($row['job_type'] ?? ''))),
                'category' => $row['category'] ?? '',
                'salary' => $row['salary'] !== null ? (float) $row['salary'] : null,
                'salaryRange' => $row['salary_range'] ?? '',
                'contactEmail' => $row['contact_email'] ?? '',
                'contactPhone' => $row['contact_phone'] ?? '',
                'applicationUrl' => $row['application_url'] ?? '',
                'active' => !empty($row['is_active']),
                'createdBy' => $row['admin_name'] ?? 'Admin',
            ];
        }

        return $items;
    }

    public static function getJobPostById(int $jobId): ?array
    {
        foreach (self::getJobPosts() as $job) {
            if ($job['jobId'] === $jobId) {
                return $job;
            }
        }
        return null;
    }

    public static function createJobPost(array $input, int $adminUserId): bool
    {
        $adminId = self::getAdminIdByUserId($adminUserId);
        $salary = is_numeric($input['salary'] ?? null) ? (float) $input['salary'] : 'NULL';
        Database::iud(
            "INSERT INTO job_posts
                (title, company, description, requirements, location, job_type, category, salary, salary_range, contact_email, contact_phone, application_url, is_active, created_by, created_at, updated_at)
             VALUES
                ('" . self::esc($input['title'] ?? '') . "',
                 '" . self::esc($input['company'] ?? '') . "',
                 '" . self::esc($input['description'] ?? '') . "',
                 '" . self::esc($input['requirements'] ?? '') . "',
                 '" . self::esc($input['location'] ?? '') . "',
                 '" . self::normalizeJobType((string) ($input['jobType'] ?? '')) . "',
                 '" . self::esc($input['category'] ?? '') . "',
                 $salary,
                 '" . self::esc($input['salaryRange'] ?? '') . "',
                 '" . self::esc($input['contactEmail'] ?? '') . "',
                 '" . self::esc($input['contactPhone'] ?? '') . "',
                 '" . self::esc($input['applicationUrl'] ?? '') . "',
                 1,
                 " . max(1, $adminId) . ",
                 NOW(),
                 NOW())"
        );
        return true;
    }

    public static function updateJobPost(int $jobId, array $input): bool
    {
        $salary = is_numeric($input['salary'] ?? null) ? (float) $input['salary'] : 'NULL';
        $isActive = !empty($input['isActive']) ? 1 : 0;
        Database::iud(
            "UPDATE job_posts
             SET title = '" . self::esc($input['title'] ?? '') . "',
                 company = '" . self::esc($input['company'] ?? '') . "',
                 description = '" . self::esc($input['description'] ?? '') . "',
                 requirements = '" . self::esc($input['requirements'] ?? '') . "',
                 location = '" . self::esc($input['location'] ?? '') . "',
                 job_type = '" . self::normalizeJobType((string) ($input['jobType'] ?? '')) . "',
                 category = '" . self::esc($input['category'] ?? '') . "',
                 salary = $salary,
                 salary_range = '" . self::esc($input['salaryRange'] ?? '') . "',
                 contact_email = '" . self::esc($input['contactEmail'] ?? '') . "',
                 contact_phone = '" . self::esc($input['contactPhone'] ?? '') . "',
                 application_url = '" . self::esc($input['applicationUrl'] ?? '') . "',
                 is_active = $isActive,
                 updated_at = NOW()
             WHERE job_id = $jobId"
        );
        return true;
    }

    public static function deleteJobPost(int $jobId): bool
    {
        Database::iud("DELETE FROM job_posts WHERE job_id = $jobId");
        return true;
    }

    private static function getAdminIdByUserId(int $userId): int
    {
        $safeUserId = max(0, $userId);
        $rs = Database::search("SELECT admin_id FROM admin WHERE user_id = $safeUserId LIMIT 1");
        return (int) ($rs && $row = $rs->fetch_assoc() ? ($row['admin_id'] ?? 0) : 0);
    }
}
