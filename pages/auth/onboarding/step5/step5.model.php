<?php

/**
 * Step 5 Model: Active Plan Retrieval
 */
class Step5Model
{
    /**
     * Retrieves the user's active recovery plan to display on the success screen.
     */
    public static function getActivePlan(int $userId): ?array
    {
        $db = Database::getConnection();

        try {
            // Find the most recently created active plan for the user
            $stmt = $db->prepare(
                "SELECT title, plan_type, start_date 
                 FROM recovery_plans 
                 WHERE user_id = ? AND status = 'active'
                 ORDER BY created_at DESC 
                 LIMIT 1"
            );
            $stmt->execute([$userId]);

            return $stmt->fetch() ?: null;
        } catch (PDOException $e) {
            error_log("Failed to get active plan: " . $e->getMessage());
            return null;
        }
    }
}
