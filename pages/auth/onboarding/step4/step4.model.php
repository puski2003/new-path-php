<?php

/**
 * Step 4 Model: Plan Selection
 */
class Step4Model
{
    /**
     * Creates a recovery plan, marks onboarding complete, advances to step 5.
     */
    public static function createPlanAndComplete(int $userId, string $planType): bool
    {
        $db = Database::getConnection();

        try {
            $db->beginTransaction();

            $title = $planType === 'counselor' ? 'Professional Counseling Path' : 'Self-Guided Journey';
            $desc  = $planType === 'counselor'
                ? 'Work with verified experts to build a tailored plan and schedule sessions.'
                : 'Take control at your own pace with system-guided goals and daily tracking.';
            $typeString = $planType === 'counselor' ? 'counselor' : 'self';

            // Insert recovery plan
            $planStmt = $db->prepare(
                "INSERT INTO recovery_plans (user_id, title, description, plan_type, status, start_date, progress_percentage) 
                 VALUES (?, ?, ?, ?, 'active', CURDATE(), 0)"
            );
            $planStmt->execute([
                $userId,
                $title,
                $desc,
                $typeString
            ]);

            // Update user onboarding state
            $onboardStmt = $db->prepare(
                "UPDATE users SET onboarding_completed = 1, current_onboarding_step = 5 WHERE user_id = ?"
            );
            $onboardStmt->execute([$userId]);

            $db->commit();
            return true;
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Failed to create plan and complete onboarding: " . $e->getMessage());
            return false;
        }
    }
}
