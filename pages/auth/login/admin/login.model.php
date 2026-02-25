<?php

/**
 * Admin Login Model
 */
class LoginModel
{
    /**
     * Find an admin user by email.
     * Admins must have the 'admin' role.
     */
    public static function findByEmail(string $email): ?array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare(
            "SELECT user_id, email, password_hash, role, display_name, first_name 
             FROM users 
             WHERE email = ? AND role = 'admin'
             LIMIT 1"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
}
