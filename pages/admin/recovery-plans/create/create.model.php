<?php

class AdminRecoveryPlanCreateModel
{
    private static function esc(string $value): string
    {
        Database::setUpConnection();
        return Database::$connection->real_escape_string($value);
    }

    public static function create(array $input): bool|array
    {
        try {
            Database::setUpConnection();
            Database::$connection->begin_transaction();

            $title = trim($input['title'] ?? '');
            if (empty($title)) {
                return ['error' => 'Plan title is required.'];
            }

            $description = self::esc(trim($input['description'] ?? ''));
            $planGoal = self::esc(trim($input['planGoal'] ?? ''));
            $category = self::esc(trim($input['category'] ?? ''));
            $customNotes = self::esc(trim($input['customNotes'] ?? ''));
            $startDate = !empty($input['startDate']) ? self::esc($input['startDate']) : null;
            $targetDate = !empty($input['targetCompletionDate']) ? self::esc($input['targetCompletionDate']) : null;

            $insertSql = "INSERT INTO recovery_plans
                 (user_id, counselor_id, title, description, category, start_date,
                  target_completion_date, plan_type, status, is_template, custom_notes,
                  progress_percentage, assigned_status, created_at, updated_at)
                 VALUES
                 (NULL, NULL, '" . self::esc($title) . "', '$description', '$category',
                  " . ($startDate ? "'$startDate'" : 'NULL') . ",
                  " . ($targetDate ? "'$targetDate'" : 'NULL') . ",
                  'counselor', 'draft', 1, '$customNotes',
                  0, NULL, NOW(), NOW())";
            
            $result = Database::iud($insertSql);
            $lastError = Database::$connection->error;
            if (!$result || $lastError) {
                error_log("ADMIN PLAN INSERT FAILED. Error: " . ($lastError ?: 'unknown'));
                throw new Exception('Database insert failed: ' . ($lastError ?: 'unknown'));
            }

            $planId = (int) Database::$connection->insert_id;
            if ($planId <= 0) {
                throw new Exception('Failed to create plan - no insert ID returned.');
            }

            if (!empty($planGoal)) {
                Database::iud(
                    "INSERT INTO recovery_goals (plan_id, phase, goal_type, title, current_progress, status, created_at, updated_at)
                     VALUES ($planId, 0, 'overall', '$planGoal', 0, 'in_progress', NOW(), NOW())"
                );
            }

            $shortTermGoalTitle = trim($input['shortTermGoalTitle'] ?? '');
            $shortTermGoalDays = max(1, (int) ($input['shortTermGoalDays'] ?? 0));
            if (!empty($shortTermGoalTitle)) {
                Database::iud(
                    "INSERT INTO recovery_goals (plan_id, phase, goal_type, title, target_days, current_progress, status, created_at, updated_at)
                     VALUES ($planId, 0, 'short_term', '" . self::esc($shortTermGoalTitle) . "', $shortTermGoalDays, 0, 'in_progress', NOW(), NOW())"
                );
            }

            $longTermGoalTitle = trim($input['longTermGoalTitle'] ?? '');
            $longTermGoalDays = max(1, (int) ($input['longTermGoalDays'] ?? 0));
            if (!empty($longTermGoalTitle)) {
                Database::iud(
                    "INSERT INTO recovery_goals (plan_id, phase, goal_type, title, target_days, current_progress, status, created_at, updated_at)
                     VALUES ($planId, 0, 'long_term', '" . self::esc($longTermGoalTitle) . "', $longTermGoalDays, 0, 'in_progress', NOW(), NOW())"
                );
            }

            $taskPhases = $input['taskPhase'] ?? [];
            $taskTitles = $input['taskTitle'] ?? [];
            $taskTypes = $input['taskType'] ?? [];
            $recurrencePatterns = $input['recurrencePattern'] ?? [];

            if (is_array($taskTitles)) {
                foreach ($taskTitles as $index => $rawTitle) {
                    $taskTitle = trim((string) $rawTitle);
                    if ($taskTitle === '') {
                        continue;
                    }

                    $taskType = self::esc((string) ($taskTypes[$index] ?? 'custom'));
                    $recurrence = self::esc((string) ($recurrencePatterns[$index] ?? ''));
                    $phase = max(1, (int) ($taskPhases[$index] ?? 1));
                    $isRecurring = $recurrence !== '' ? 1 : 0;

                    Database::iud(
                        "INSERT INTO recovery_tasks (plan_id, title, task_type, status, priority, phase,
                          is_recurring, recurrence_pattern, sort_order, created_at, updated_at)
                         VALUES ($planId, '" . self::esc($taskTitle) . "', '$taskType', 'pending', 'medium',
                          $phase, $isRecurring, " . ($isRecurring && $recurrence ? "'$recurrence'" : 'NULL') . ",
                          $index, NOW(), NOW())"
                    );
                }
            }

            for ($p = 1; $p <= 3; $p++) {
                $milestones = $input["phase{$p}Milestone"] ?? [];
                if (is_array($milestones)) {
                    foreach ($milestones as $milestone) {
                        $milestoneTitle = trim((string) $milestone);
                        if ($milestoneTitle === '') {
                            continue;
                        }
                        Database::iud(
                            "INSERT INTO recovery_goals (plan_id, phase, goal_type, title, current_progress, status, created_at, updated_at)
                             VALUES ($planId, $p, 'milestone', '" . self::esc($milestoneTitle) . "', 0, 'in_progress', NOW(), NOW())"
                        );
                    }
                }
            }

            Database::$connection->commit();
            return true;

        } catch (Exception $e) {
            Database::$connection->rollback();
            error_log("EXCEPTION in AdminRecoveryPlanCreateModel::create: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
