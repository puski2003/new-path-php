<?php

/**
 * Counselor Login Model
 */
class LoginModel
{
    /**
     * Find a counselor user by email.
     * Counselors must have the 'counselor' role.
     */
    public static function findByEmail(string $email): ?array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare(
            "SELECT user_id, email, password_hash, role, display_name, first_name 
             FROM users 
             WHERE email = ? AND role = 'counselor'
             LIMIT 1"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
}
