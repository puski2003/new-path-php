<?php

class PostRecoveryModel
{
    public static function getJobs(int $userId, array $params = []): array
    {
        $q = trim((string)($params['q'] ?? ''));
        $onlySaved = !empty($params['onlySaved']);
        $page = max(1, (int)($params['page'] ?? 1));
        $perPage = max(1, min(50, (int)($params['perPage'] ?? 8)));
        $offset = ($page - 1) * $perPage;

        $where = "WHERE jp.is_active = 1";
        if ($q !== '') {
            $sq = addslashes($q);
            $where .= " AND (jp.title LIKE '%$sq%' OR jp.company LIKE '%$sq%' OR jp.location LIKE '%$sq%' OR jp.category LIKE '%$sq%')";
        }

        $joinSaved = "LEFT JOIN saved_jobs sj ON sj.job_id = jp.job_id AND sj.user_id = $userId";
        if ($onlySaved) {
            $where .= " AND sj.saved_id IS NOT NULL";
        }

        $countRs = Database::search(
            "SELECT COUNT(*) AS total
             FROM job_posts jp
             $joinSaved
             $where"
        );
        $countRow = $countRs->fetch_assoc();
        $total = (int)($countRow['total'] ?? 0);

        $rs = Database::search(
            "SELECT jp.job_id, jp.title, jp.company, jp.description, jp.location, jp.job_type, jp.salary, jp.salary_range,
                    jp.contact_email, jp.application_url, jp.created_at,
                    CASE WHEN sj.saved_id IS NULL THEN 0 ELSE 1 END AS is_saved
             FROM job_posts jp
             $joinSaved
             $where
             ORDER BY jp.created_at DESC
             LIMIT $perPage OFFSET $offset"
        );

        $items = [];
        while ($row = $rs->fetch_assoc()) {
            $items[] = [
                'jobId' => (int)$row['job_id'],
                'title' => $row['title'] ?? 'Untitled Job',
                'company' => $row['company'] ?? 'Unknown Company',
                'description' => $row['description'] ?? '',
                'location' => $row['location'] ?? '',
                'jobType' => self::formatJobType((string)($row['job_type'] ?? 'full_time')),
                'salary' => $row['salary'],
                'salaryRange' => $row['salary_range'] ?? '',
                'contactEmail' => $row['contact_email'] ?? '',
                'applicationUrl' => $row['application_url'] ?? '',
                'isSaved' => (int)($row['is_saved'] ?? 0) === 1,
            ];
        }

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'totalPages' => max(1, (int)ceil($total / $perPage)),
        ];
    }

    public static function toggleSaveJob(int $userId, int $jobId): bool
    {
        if ($jobId <= 0) return false;

        $existsRs = Database::search(
            "SELECT saved_id
             FROM saved_jobs
             WHERE user_id = $userId
               AND job_id = $jobId
             LIMIT 1"
        );
        $exists = $existsRs->fetch_assoc();

        if ($exists) {
            Database::iud(
                "DELETE FROM saved_jobs
                 WHERE user_id = $userId
                   AND job_id = $jobId"
            );
            return false;
        }

        Database::iud(
            "INSERT INTO saved_jobs (user_id, job_id, created_at)
             VALUES ($userId, $jobId, NOW())"
        );
        return true;
    }

    private static function formatJobType(string $jobType): string
    {
        return match ($jobType) {
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract' => 'Contract',
            'temporary' => 'Temporary',
            'internship' => 'Internship',
            default => 'Job',
        };
    }
}

