<?php

class SupportGroupsModel
{
    private static function esc(string $value): string
    {
        Database::setUpConnection();
        return Database::$connection->real_escape_string($value);
    }

    public static function getGroups(array $filters): array
    {
        $where = ['1=1'];
        $status = trim((string) ($filters['status'] ?? 'all'));
        if ($status === 'Active') {
            $where[] = 'sg.is_active = 1';
        } elseif ($status === 'Archived') {
            $where[] = 'sg.is_active = 0';
        }
        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $safeSearch = self::esc($search);
            $where[] = "(sg.name LIKE '%$safeSearch%' OR sg.description LIKE '%$safeSearch%')";
        }

        $rs = Database::search(
            "SELECT sg.*, COUNT(sgm.membership_id) AS member_count, a.full_name AS admin_name
             FROM support_groups sg
             LEFT JOIN support_group_members sgm ON sgm.group_id = sg.group_id
             LEFT JOIN admin a ON a.admin_id = sg.created_by
             WHERE " . implode(' AND ', $where) . "
             GROUP BY sg.group_id
             ORDER BY sg.created_at DESC"
        );

        $groups = [];
        while ($rs && ($row = $rs->fetch_assoc())) {
            $groups[] = [
                'groupId' => (int) $row['group_id'],
                'groupName' => $row['name'] ?? '',
                'description' => $row['description'] ?? '',
                'type' => !empty($row['meeting_link']) ? 'Public' : 'Private',
                'members' => (int) ($row['member_count'] ?? 0),
                'nextSession' => $row['meeting_schedule'] ?? 'To be scheduled',
                'createdBy' => $row['admin_name'] ?? 'Admin',
                'status' => !empty($row['is_active']) ? 'Active' : 'Archived',
            ];
        }

        return $groups;
    }

    public static function getUpcomingSessions(): array
    {
        return [
            ['day' => 'Mon', 'time' => '6:00 PM', 'title' => 'Peer Support Circle'],
            ['day' => 'Wed', 'time' => '7:30 PM', 'title' => 'Family Recovery Group'],
            ['day' => 'Sat', 'time' => '10:00 AM', 'title' => 'New Members Orientation'],
        ];
    }
}
