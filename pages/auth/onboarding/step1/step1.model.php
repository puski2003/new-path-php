<?php

/**
 * Step 1 Model: Create the user.
 */
class Step1Model
{
    public static function emailExists(string $email): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT 1 FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return (bool)$stmt->fetch();
    }

    public static function createUser(string $email, string $password, string $displayName, ?int $age, ?string $gender): ?array
    {
        $db = Database::getConnection();

        $salt = password_hash(random_bytes(16), PASSWORD_BCRYPT);
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $db->prepare(
            "INSERT INTO users (email, password_hash, salt, display_name, age, gender, role, onboarding_completed, current_onboarding_step) 
             VALUES (?, ?, ?, ?, ?, ?, 'user', 0, 2)"
        );

        try {
            $stmt->execute([
                $email,
                $hash,
                $salt,
                $displayName,
                $age,
                $gender
            ]);

            $id = $db->lastInsertId();

            // Fetch immediately to ensure we have all required JWT fields
            $fetchStmt = $db->prepare("SELECT user_id, email, display_name, role FROM users WHERE user_id = ?");
            $fetchStmt->execute([$id]);
            return $fetchStmt->fetch() ?: null;
        } catch (PDOException $e) {
            error_log("Failed to create user: " . $e->getMessage());
            return null;
        }
    }
}
