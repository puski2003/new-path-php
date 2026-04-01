<?php

/**
 * Step 4 Model: Plan Selection
 */
class Step4Model
{
    /**
     * Creates a recovery plan, marks onboarding complete, advances to step 5.
     */
    /**
     * Fetch the stored risk score for a user (defaults to 5 = LOW if not yet scored).
     */
    public static function getRiskScore(int $userId): int
    {
        Database::setUpConnection();
        $uid = (int) $userId;

        $rs  = Database::search("SELECT risk_score FROM user_profiles WHERE user_id = $uid LIMIT 1");
        $row = $rs->fetch_assoc();

        return (int) ($row['risk_score'] ?? 5);
    }

    public static function createPlanAndComplete(int $userId, string $planType): bool
    {
        Database::setUpConnection();

        $safeUserId = (int) $userId;

        $title = $planType === 'counselor' ? 'Professional Counseling Path' : 'Self-Guided Journey';
        $desc  = $planType === 'counselor'
            ? 'Work with verified experts to build a tailored plan and schedule sessions.'
            : 'Take control at your own pace with system-guided goals and daily tracking.';
        $typeString = $planType === 'counselor' ? 'counselor' : 'self';

        $safeTitle = Database::$connection->real_escape_string($title);
        $safeDesc  = Database::$connection->real_escape_string($desc);
        $safeType  = Database::$connection->real_escape_string($typeString);

        // Insert recovery plan
        Database::iud(
            "INSERT INTO recovery_plans (user_id, title, description, plan_type, status, start_date, progress_percentage) 
             VALUES ($safeUserId, '$safeTitle', '$safeDesc', '$safeType', 'active', CURDATE(), 0)"
        );

        // Update user onboarding state
        Database::iud(
            "UPDATE users SET onboarding_completed = 1, current_onboarding_step = 5 WHERE user_id = $safeUserId"
        );

        return true;
    }
}
