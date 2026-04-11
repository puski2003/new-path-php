<?php

class FinancesModel
{
    private static function esc(string $value): string
    {
        Database::setUpConnection();
        return Database::$connection->real_escape_string($value);
    }

    public static function getSummary(): array
    {
        $revenue = Database::search("SELECT COALESCE(SUM(amount), 0) AS total, COUNT(*) AS sessions_paid, COALESCE(AVG(amount), 0) AS avg_payment FROM transactions WHERE status = 'completed'");
        $pendingRefunds = Database::search("SELECT COUNT(*) AS total FROM refund_disputes WHERE status = 'pending'");
        $row = $revenue ? ($revenue->fetch_assoc() ?: []) : [];
        $refundRow = $pendingRefunds ? ($pendingRefunds->fetch_assoc() ?: []) : [];

        return [
            'totalRevenue' => (float) ($row['total'] ?? 0),
            'sessionsPaid' => (int) ($row['sessions_paid'] ?? 0),
            'avgPayment' => (float) ($row['avg_payment'] ?? 0),
            'pendingRefunds' => (int) ($refundRow['total'] ?? 0),
        ];
    }

    public static function getDisputes(array $filters): array
    {
        $where = ['1=1'];
        $status = trim((string) ($filters['disputeStatus'] ?? 'all'));
        if ($status !== '' && $status !== 'all') {
            $where[] = "rd.status = '" . self::esc($status) . "'";
        }
        $issue = trim((string) ($filters['disputeIssue'] ?? 'allIssues'));
        if ($issue !== '' && $issue !== 'allIssues') {
            $where[] = "rd.issue_type = '" . self::esc(strtolower(str_replace(' ', '_', $issue))) . "'";
        }

        $rs = Database::search(
            "SELECT rd.*, t.transaction_uuid, t.amount,
                    COALESCE(u.display_name, u.email) AS user_name,
                    COALESCE(cu.display_name, cu.email, 'Unassigned') AS counselor_name
             FROM refund_disputes rd
             INNER JOIN transactions t ON t.transaction_id = rd.transaction_id
             INNER JOIN users u ON u.user_id = rd.user_id
             LEFT JOIN counselors c ON c.counselor_id = t.counselor_id
             LEFT JOIN users cu ON cu.user_id = c.user_id
             WHERE " . implode(' AND ', $where) . "
             ORDER BY rd.created_at DESC"
        );

        $items = [];
        while ($rs && ($row = $rs->fetch_assoc())) {
            $items[] = [
                'transactionId' => $row['transaction_uuid'] ?: ('TXN-' . $row['transaction_id']),
                'userName' => $row['user_name'] ?? 'User',
                'counselorName' => $row['counselor_name'] ?? 'Unassigned',
                'amount' => number_format((float) ($row['amount'] ?? 0), 2),
                'issue' => ucwords(str_replace('_', ' ', (string) ($row['issue_type'] ?? 'other'))),
                'status' => $row['status'] ?? 'pending',
            ];
        }

        if ($items === []) {
            $items[] = [
                'transactionId' => 'TXN-DEMO-01',
                'userName' => 'Demo User',
                'counselorName' => 'Demo Counselor',
                'amount' => '45.00',
                'issue' => 'Technical Issue',
                'status' => 'pending',
            ];
        }

        return $items;
    }

    public static function getTransactions(string $search = ''): array
    {
        $where = ['1=1'];
        if ($search !== '') {
            $safeSearch = self::esc($search);
            $where[] = "(t.transaction_uuid LIKE '%$safeSearch%' OR u.email LIKE '%$safeSearch%' OR COALESCE(cu.email, '') LIKE '%$safeSearch%')";
        }

        $rs = Database::search(
            "SELECT t.*, COALESCE(u.display_name, u.email) AS user_name,
                    COALESCE(cu.display_name, cu.email, 'Unassigned') AS counselor_name
             FROM transactions t
             INNER JOIN users u ON u.user_id = t.user_id
             LEFT JOIN counselors c ON c.counselor_id = t.counselor_id
             LEFT JOIN users cu ON cu.user_id = c.user_id
             WHERE " . implode(' AND ', $where) . "
             ORDER BY t.created_at DESC"
        );

        $items = [];
        while ($rs && ($row = $rs->fetch_assoc())) {
            $items[] = [
                'transactionId' => $row['transaction_uuid'] ?: ('TXN-' . $row['transaction_id']),
                'userName' => $row['user_name'] ?? 'User',
                'counselorName' => $row['counselor_name'] ?? 'Unassigned',
                'date' => !empty($row['created_at']) ? date('M j, Y', strtotime($row['created_at'])) : '-',
                'amount' => number_format((float) ($row['amount'] ?? 0), 2),
                'paymentMethod' => ucfirst((string) ($row['payment_type'] ?? 'session')),
                'status' => $row['status'] ?? 'pending',
            ];
        }
        return $items;
    }
}
