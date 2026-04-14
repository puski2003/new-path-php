<?php

class SystemSecurityModel
{
    private static function esc(string $value): string
    {
        Database::setUpConnection();
        return Database::$connection->real_escape_string($value);
    }

    public static function getLogs(array $filters): array
    {
        $where = ['1=1'];
        $action = trim((string) ($filters['action'] ?? 'all'));
        if ($action !== '' && $action !== 'all') {
            $where[] = "action = '" . self::esc($action) . "'";
        }
        $startDate = trim((string) ($filters['startDate'] ?? ''));
        if ($startDate !== '') {
            $where[] = "DATE(created_at) >= '" . self::esc($startDate) . "'";
        }
        $endDate = trim((string) ($filters['endDate'] ?? ''));
        if ($endDate !== '') {
            $where[] = "DATE(created_at) <= '" . self::esc($endDate) . "'";
        }

        $rs = Database::search(
            "SELECT al.*, COALESCE(u.display_name, u.email, 'System Admin') AS admin_name
             FROM audit_logs al
             LEFT JOIN users u ON u.user_id = al.user_id
             WHERE " . implode(' AND ', $where) . "
             ORDER BY al.created_at DESC
             LIMIT 50"
        );

        $logs = [];
        while ($rs && ($row = $rs->fetch_assoc())) {
            $logs[] = [
                'dateTime' => !empty($row['created_at']) ? date('M j, Y g:i A', strtotime($row['created_at'])) : '-',
                'action' => $row['action'] ?? 'System event',
                'adminName' => $row['admin_name'] ?? 'Admin',
                'affectedResource' => trim(($row['entity_type'] ?? 'resource') . ' #' . ($row['entity_id'] ?? '-')),
                'status' => 'Completed',
            ];
        }

        if ($logs === []) {
            $logs[] = [
                'dateTime' => date('M j, Y g:i A'),
                'action' => 'Viewed admin dashboard',
                'adminName' => 'System Admin',
                'affectedResource' => 'dashboard',
                'status' => 'Completed',
            ];
        }

        return $logs;
    }
}
