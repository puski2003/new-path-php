<?php

/**
 * Login Model — queries the DB to find a user by email.
 * Returns the raw user row (with hashed password) or null.
 */
class LoginModel
{

    public static function findByEmail(string $email): ?array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare('SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
}
