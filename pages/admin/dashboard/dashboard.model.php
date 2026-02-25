<?php

/**
 * Admin Dashboard Model — DB queries only.
 * Returns plain arrays — no HTML, no logic.
 */
class DashboardModel
{

    public static function getSummary(): array
    {
        $db = Database::getConnection();

        // Total active users
        $totalUsers = $db->query('SELECT COUNT(*) FROM users WHERE role = "user"')->fetchColumn();

        // Pending counselor applications
        $pendingApplications = $db->query('SELECT COUNT(*) FROM counselor_applications WHERE status = "pending"')->fetchColumn();

        // Upcoming sessions (next 24 hours) — column is session_datetime
        $upcomingSessions = $db->prepare('SELECT COUNT(*) FROM sessions WHERE session_datetime BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)');
        $upcomingSessions->execute();

        // Revenue today
        $revenueToday = $db->query('SELECT COALESCE(SUM(amount), 0) FROM transactions WHERE DATE(created_at) = CURDATE() AND status = "completed"')->fetchColumn();

        return [
            'totalUsers'          => (int) $totalUsers,
            'pendingApplications' => (int) $pendingApplications,
            'upcomingSessions'    => (int) $upcomingSessions->fetchColumn(),
            'revenueToday'        => (float) $revenueToday,
        ];
    }
}
