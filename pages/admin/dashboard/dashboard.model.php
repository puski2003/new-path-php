<?php

/**
 * Admin Dashboard Model — DB queries only.
 * Returns plain arrays — no HTML, no logic.
 */
class DashboardModel
{
    public static function getSummary(): array
    {
        // Total active users
        $rs1 = Database::search('SELECT COUNT(*) AS cnt FROM users WHERE role = "user"');
        $totalUsers = $rs1->fetch_assoc()['cnt'];

        // Pending counselor applications
        $rs2 = Database::search('SELECT COUNT(*) AS cnt FROM counselor_applications WHERE status = "pending"');
        $pendingApplications = $rs2->fetch_assoc()['cnt'];

        // Upcoming sessions (next 24 hours)
        $rs3 = Database::search('SELECT COUNT(*) AS cnt FROM sessions WHERE session_datetime BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)');
        $upcomingSessions = $rs3->fetch_assoc()['cnt'];

        // Revenue today
        $rs4 = Database::search('SELECT COALESCE(SUM(amount), 0) AS revenue FROM transactions WHERE DATE(created_at) = CURDATE() AND status = "completed"');
        $revenueToday = $rs4->fetch_assoc()['revenue'];

        return [
            'totalUsers'          => (int) $totalUsers,
            'pendingApplications' => (int) $pendingApplications,
            'upcomingSessions'    => (int) $upcomingSessions,
            'revenueToday'        => (float) $revenueToday,
        ];
    }
}
