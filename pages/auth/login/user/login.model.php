<?php

/**
 * Login Model — queries the DB to find a user by email.
 * Column names match the actual `users` table schema:
 *   user_id, email, password_hash, role, display_name, first_name
 */
class LoginModel
{
    public static function findByEmail(string $email): ?array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT user_id, email, password_hash, role, display_name, first_name
             FROM users
             WHERE email = ?
             LIMIT 1'
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
}
