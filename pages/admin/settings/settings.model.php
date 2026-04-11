<?php

class SettingsModel
{
    private static function esc(string $value): string
    {
        Database::setUpConnection();
        return Database::$connection->real_escape_string($value);
    }

    public static function getSettings(): array
    {
        $defaults = [
            'platform_name' => 'New Path Recovery',
            'session_length' => '60',
            'email_notifications' => '1',
            'sms_notifications' => '0',
            'current_logo' => 'logo.svg',
        ];

        $rs = Database::search("SELECT setting_key, setting_value FROM system_settings");
        while ($rs && ($row = $rs->fetch_assoc())) {
            $defaults[$row['setting_key']] = (string) ($row['setting_value'] ?? '');
        }

        return [
            'platformName' => $defaults['platform_name'],
            'sessionLength' => (int) $defaults['session_length'],
            'emailNotificationsEnabled' => $defaults['email_notifications'] === '1',
            'smsNotificationsEnabled' => $defaults['sms_notifications'] === '1',
            'currentLogo' => $defaults['current_logo'],
        ];
    }

    public static function save(array $input, int $adminUserId): bool
    {
        $entries = [
            'platform_name' => trim((string) ($input['platformName'] ?? 'New Path Recovery')),
            'session_length' => trim((string) ($input['sessionLength'] ?? '60')),
            'email_notifications' => !empty($input['emailNotifications']) ? '1' : '0',
            'sms_notifications' => !empty($input['smsNotifications']) ? '1' : '0',
            'current_logo' => 'logo.svg',
        ];

        foreach ($entries as $key => $value) {
            $safeKey = self::esc($key);
            $safeValue = self::esc($value);
            Database::iud(
                "INSERT INTO system_settings (setting_key, setting_value, updated_by, updated_at)
                 VALUES ('$safeKey', '$safeValue', $adminUserId, NOW())
                 ON DUPLICATE KEY UPDATE setting_value = '$safeValue', updated_by = $adminUserId, updated_at = NOW()"
            );
        }

        return true;
    }

    public static function getRoles(): array
    {
        $rs = Database::search(
            "SELECT a.full_name, a.permissions, a.is_super_admin, COUNT(a2.admin_id) AS assigned_admins
             FROM admin a
             LEFT JOIN admin a2 ON a2.is_super_admin = a.is_super_admin
             GROUP BY a.admin_id
             ORDER BY a.is_super_admin DESC, a.full_name ASC"
        );

        $roles = [];
        while ($rs && ($row = $rs->fetch_assoc())) {
            $roles[] = [
                'roleName' => !empty($row['is_super_admin']) ? 'Super Admin' : 'Platform Admin',
                'permissions' => !empty($row['permissions']) ? (json_decode($row['permissions'], true) ?: ['Manage Platform']) : (!empty($row['is_super_admin']) ? ['All'] : ['Users', 'Content', 'Reports']),
                'assignedAdmins' => (int) ($row['assigned_admins'] ?? 1),
            ];
        }

        if ($roles === []) {
            $roles[] = ['roleName' => 'Super Admin', 'permissions' => ['All'], 'assignedAdmins' => 1];
        }

        return $roles;
    }
}
