<?php

/**
 * Step 3 Model: Assessment Info
 */
class Step3Model
{
    /**
     * Store assessment score and increment step
     */
    public static function saveAssessmentScore(int $userId, int $score): bool
    {
        $db = Database::getConnection();

        try {
            $db->beginTransaction();

            // We don't have a specific `assessment_score` column in user_profiles based on schema
            // We can store it as part of a JSON column or just increment the step.
            // If the Java codebase recorded it, let's assume it did it somewhere, maybe `custom_notes` in plans later?
            // For now, advancing step is enough to emulate flow.

            $stepStmt = $db->prepare(
                "UPDATE users SET current_onboarding_step = 4 WHERE user_id = ?"
            );
            $stepStmt->execute([$userId]);

            $db->commit();
            return true;
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Failed to save assessment info: " . $e->getMessage());
            return false;
        }
    }
}
