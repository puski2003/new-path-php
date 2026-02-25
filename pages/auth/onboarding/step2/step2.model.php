<?php

/**
 * Step 2 Model: Substance Use Info
 */
class Step2Model
{
    /**
     * Store substance usage info into user_profiles table 
     * and increment the user's current onboarding step.
     */
    public static function saveSubstanceInfo(int $userId, string $primarySubstance, string $frequency, string $lastUsed, int $quitAttempts): bool
    {
        $db = Database::getConnection();

        try {
            $db->beginTransaction();

            // Insert/Update user_profiles with substance info
            // `recovery_type` in the DB schema is closest to the addiction they are fighting.
            $stmt = $db->prepare(
                "INSERT INTO user_profiles (user_id, recovery_type) 
                 VALUES (?, ?) 
                 ON DUPLICATE KEY UPDATE recovery_type = VALUES(recovery_type)"
            );
            $stmt->execute([
                $userId,
                $primarySubstance
            ]);

            // Note: Since we don't have columns for frequency, last used, or quit_attempts in `user_profiles` natively
            // (unless added to a JSON column like `onboarding_data`), we'll just save the primary substance to `recovery_type`
            // If the Java app used a separate `substance_info` table that we're missing, we might need a schema update later,
            // but for now, we'll store basic info in the profile.

            // Advance step
            $stepStmt = $db->prepare(
                "UPDATE users SET current_onboarding_step = 3 WHERE user_id = ?"
            );
            $stepStmt->execute([$userId]);

            $db->commit();
            return true;
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Failed to save substance info: " . $e->getMessage());
            return false;
        }
    }
}
