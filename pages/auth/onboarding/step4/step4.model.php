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

        if ($planType === 'self') {
            $newPlanId = (int) Database::$connection->insert_id;
            if ($newPlanId > 0) {
                $riskScore = self::getRiskScore($safeUserId);
                if ($riskScore >= 12)    { $riskBand = 'HIGH'; }
                elseif ($riskScore >= 8) { $riskBand = 'MODERATE'; }
                else                     { $riskBand = 'LOW'; }

                self::seedPlanDefaults($newPlanId, $riskBand);
            }
        }

        // Update user onboarding state
        Database::iud(
            "UPDATE users SET onboarding_completed = 1, current_onboarding_step = 5 WHERE user_id = $safeUserId"
        );

        return true;
    }

    private static function seedPlanDefaults(int $planId, string $riskBand): void
    {
        $shortGoal = 'Build a consistent daily recovery routine';
        $longGoal  = $riskBand === 'HIGH'
            ? 'Achieve 90 days of sobriety with counselor support'
            : 'Achieve 30 days of sobriety independently';

        $safeShort = addslashes($shortGoal);
        $safeLong  = addslashes($longGoal);
        $longDays  = $riskBand === 'HIGH' ? 90 : 30;

        Database::iud(
            "INSERT INTO recovery_goals (plan_id, goal_type, title, target_days, current_progress, status, created_at, updated_at)
             VALUES
               ($planId, 'short_term', '$safeShort', 14, 0, 'in_progress', NOW(), NOW()),
               ($planId, 'long_term',  '$safeLong',  $longDays, 0, 'in_progress', NOW(), NOW())"
        );

        $tasks = [
            ['Complete your daily check-in',     'custom',   1],
            ['Log any urges you experience',      'custom',   1],
            ['Write one journal entry this week', 'journal',  1],
        ];

        if ($riskBand === 'MODERATE') {
            $tasks[] = ['Read one recovery article or resource',  'custom',   1];
            $tasks[] = ['Book an introductory counselor session', 'session',  2];
        }

        if ($riskBand === 'HIGH') {
            $tasks[] = ['Schedule your first counselor session',  'session',  1];
            $tasks[] = ['Complete counselor intake assessment',   'session',  2];
            $tasks[] = ['Identify your top 3 personal triggers', 'custom',   2];
        }

        foreach ($tasks as $i => [$title, $type, $phase]) {
            $t = addslashes($title);
            Database::iud(
                "INSERT INTO recovery_tasks
                    (plan_id, title, task_type, status, priority, phase, sort_order, created_at, updated_at)
                 VALUES ($planId, '$t', '$type', 'pending', 'medium', $phase, $i, NOW(), NOW())"
            );
        }
    }
}
