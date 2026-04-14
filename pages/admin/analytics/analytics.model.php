<?php

class AnalyticsModel
{
    public static function getSummary(): array
    {
        $users = Database::search("SELECT COUNT(*) AS total FROM users WHERE role = 'user'");
        $sessions = Database::search("SELECT COUNT(*) AS total FROM sessions");
        $plans = Database::search("SELECT COUNT(*) AS total, COALESCE(AVG(progress_percentage), 0) AS avg_progress FROM recovery_plans");
        $jobs = Database::search("SELECT COUNT(*) AS total FROM job_posts WHERE is_active = 1");

        $usersRow = $users ? ($users->fetch_assoc() ?: []) : [];
        $sessionsRow = $sessions ? ($sessions->fetch_assoc() ?: []) : [];
        $plansRow = $plans ? ($plans->fetch_assoc() ?: []) : [];
        $jobsRow = $jobs ? ($jobs->fetch_assoc() ?: []) : [];

        return [
            'totalUsers' => (int) ($usersRow['total'] ?? 0),
            'totalSessions' => (int) ($sessionsRow['total'] ?? 0),
            'totalPlans' => (int) ($plansRow['total'] ?? 0),
            'avgPlanProgress' => round((float) ($plansRow['avg_progress'] ?? 0), 1),
            'activeJobs' => (int) ($jobsRow['total'] ?? 0),
        ];
    }
}
