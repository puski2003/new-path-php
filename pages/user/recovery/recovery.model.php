<?php

class RecoveryModel
{
    public static function getUserDailyTasks(int $userId): array
    {
        $rs = Database::search(
            "SELECT rt.task_id, rt.title, rt.status, rt.priority, rt.task_type, rt.due_date
             FROM recovery_tasks rt
             INNER JOIN recovery_plans rp ON rp.plan_id = rt.plan_id
             WHERE rp.user_id = $userId
               AND rp.status = 'active'
             ORDER BY rt.status ASC, rt.priority DESC, rt.sort_order ASC, rt.task_id ASC"
        );

        $tasks = [];
        while ($row = $rs->fetch_assoc()) {
            $tasks[] = [
                'taskId' => (int)$row['task_id'],
                'title' => $row['title'] ?? 'Task',
                'status' => $row['status'] ?? 'pending',
                'priority' => $row['priority'] ?? 'medium',
                'taskType' => $row['task_type'] ?? 'custom',
                'dueDate' => !empty($row['due_date']) ? date('M j', strtotime($row['due_date'])) : null,
            ];
        }

        return $tasks;
    }

    public static function getUserTaskStats(int $userId): array
    {
        $rs = Database::search(
            "SELECT
                SUM(CASE WHEN rt.status = 'completed' THEN 1 ELSE 0 END) AS completed_count,
                SUM(CASE WHEN rt.status <> 'completed' THEN 1 ELSE 0 END) AS pending_count
             FROM recovery_tasks rt
             INNER JOIN recovery_plans rp ON rp.plan_id = rt.plan_id
             WHERE rp.user_id = $userId
               AND rp.status = 'active'"
        );

        $row = $rs->fetch_assoc();
        return [
            'completed' => isset($row['completed_count']) ? (int)$row['completed_count'] : 0,
            'pending' => isset($row['pending_count']) ? (int)$row['pending_count'] : 0,
        ];
    }

    public static function getUserActivePlans(int $userId): array
    {
        $rs = Database::search(
            "SELECT plan_id, title, progress_percentage, assigned_status, counselor_id
             FROM recovery_plans
             WHERE user_id = $userId
               AND status = 'active'
             ORDER BY updated_at DESC"
        );

        $plans = [];
        while ($row = $rs->fetch_assoc()) {
            $plans[] = [
                'planId' => (int)$row['plan_id'],
                'title' => $row['title'] ?? 'Recovery Plan',
                'progressPercentage' => (int)($row['progress_percentage'] ?? 0),
                'assignedStatus' => $row['assigned_status'] ?? null,
                'counselorId' => isset($row['counselor_id']) ? (int)$row['counselor_id'] : null,
            ];
        }

        return $plans;
    }

    public static function getAssignedPlansForUser(int $userId): array
    {
        $rs = Database::search(
            "SELECT plan_id, title
             FROM recovery_plans
             WHERE user_id = $userId
               AND assigned_status = 'pending'
             ORDER BY updated_at DESC"
        );

        $plans = [];
        while ($row = $rs->fetch_assoc()) {
            $plans[] = [
                'planId' => (int)$row['plan_id'],
                'title' => $row['title'] ?? 'Recovery Plan',
            ];
        }

        return $plans;
    }

    public static function getGoalsByPlanId(int $planId): array
    {
        $rs = Database::search(
            "SELECT goal_type, title, target_days, current_progress
             FROM recovery_goals
             WHERE plan_id = $planId
             ORDER BY goal_id ASC"
        );

        $goals = [];
        while ($row = $rs->fetch_assoc()) {
            $targetDays = max(1, (int)($row['target_days'] ?? 0));
            $current = max(0, (int)($row['current_progress'] ?? 0));
            $goals[] = [
                'goalType' => $row['goal_type'] ?? 'short_term',
                'title' => $row['title'] ?? 'Goal',
                'targetDays' => $targetDays,
                'currentProgress' => $current,
                'progressPercentage' => min(100, (int)round(($current / $targetDays) * 100)),
            ];
        }

        return $goals;
    }

    public static function getProgressStats(int $userId): array
    {
        $stats = [
            'daysSober' => 0,
            'totalDaysTracked' => 0,
            'urgesLogged' => 0,
            'sessionsCompleted' => 0,
        ];

        $rsSober = Database::search(
            "SELECT days_sober FROM user_progress
             WHERE user_id = $userId
             ORDER BY date DESC, progress_id DESC
             LIMIT 1"
        );
        if ($row = $rsSober->fetch_assoc()) {
            $stats['daysSober'] = max(0, (int)$row['days_sober']);
            $stats['totalDaysTracked'] = $stats['daysSober'];
        } else {
            $rsProfile = Database::search(
                "SELECT DATEDIFF(CURDATE(), up.sobriety_start_date) AS days
                 FROM user_profiles up
                 WHERE up.user_id = $userId
                   AND up.sobriety_start_date IS NOT NULL
                 LIMIT 1"
            );
            if ($p = $rsProfile->fetch_assoc()) {
                $stats['daysSober'] = max(0, (int)($p['days'] ?? 0));
                $stats['totalDaysTracked'] = $stats['daysSober'];
            }
        }

        $rsUrges = Database::search("SELECT COUNT(*) AS urge_count FROM urge_logs WHERE user_id = $userId");
        if ($u = $rsUrges->fetch_assoc()) {
            $stats['urgesLogged'] = (int)($u['urge_count'] ?? 0);
        }

        $rsSessions = Database::search(
            "SELECT COUNT(*) AS session_count
             FROM sessions
             WHERE user_id = $userId
               AND status = 'completed'"
        );
        if ($s = $rsSessions->fetch_assoc()) {
            $stats['sessionsCompleted'] = (int)($s['session_count'] ?? 0);
        }

        return $stats;
    }

    public static function getNextSessionSummary(int $userId): array
    {
        $rs = Database::search(
            "SELECT s.session_datetime,
                    COALESCE(u.display_name, CONCAT(u.first_name, ' ', u.last_name)) AS counselor_name
             FROM sessions s
             INNER JOIN counselors c ON c.counselor_id = s.counselor_id
             INNER JOIN users u ON u.user_id = c.user_id
             WHERE s.user_id = $userId
               AND s.session_datetime >= NOW()
               AND s.status IN ('scheduled', 'confirmed')
             ORDER BY s.session_datetime ASC
             LIMIT 1"
        );

        if ($row = $rs->fetch_assoc()) {
            return [
                'time' => date('M j, g:i A', strtotime($row['session_datetime'])),
                'counselorName' => $row['counselor_name'] ?? 'Counselor',
            ];
        }

        return [
            'time' => 'No upcoming sessions',
            'counselorName' => 'Counselor',
        ];
    }

    public static function getPlanByIdForUser(int $planId, int $userId): ?array
    {
        $rs = Database::search(
            "SELECT plan_id, title, description, plan_type, status, assigned_status, progress_percentage
             FROM recovery_plans
             WHERE plan_id = $planId
               AND user_id = $userId
             LIMIT 1"
        );

        if (!$rs || $rs->num_rows === 0) {
            return null;
        }

        $row = $rs->fetch_assoc();
        return [
            'planId' => (int)$row['plan_id'],
            'title' => $row['title'] ?? 'Recovery Plan',
            'description' => $row['description'] ?? '',
            'planType' => $row['plan_type'] ?? 'self',
            'status' => $row['status'] ?? 'draft',
            'assignedStatus' => $row['assigned_status'] ?? null,
            'progressPercentage' => (int)($row['progress_percentage'] ?? 0),
        ];
    }

    public static function acceptAssignedPlan(int $planId, int $userId): bool
    {
        if ($planId <= 0 || $userId <= 0) return false;

        Database::iud(
            "UPDATE recovery_plans
             SET assigned_status = 'accepted',
                 status = 'active',
                 start_date = COALESCE(start_date, CURDATE()),
                 updated_at = NOW()
             WHERE plan_id = $planId
               AND user_id = $userId
               AND assigned_status = 'pending'"
        );

        return true;
    }

    public static function rejectAssignedPlan(int $planId, int $userId): bool
    {
        if ($planId <= 0 || $userId <= 0) return false;

        Database::iud(
            "UPDATE recovery_plans
             SET assigned_status = 'rejected',
                 updated_at = NOW()
             WHERE plan_id = $planId
               AND user_id = $userId
               AND assigned_status = 'pending'"
        );

        return true;
    }

    public static function completeTask(int $taskId, int $userId): bool
    {
        if ($taskId <= 0 || $userId <= 0) return false;

        Database::iud(
            "UPDATE recovery_tasks rt
             INNER JOIN recovery_plans rp ON rp.plan_id = rt.plan_id
             SET rt.status = 'completed',
                 rt.completed_at = NOW(),
                 rt.updated_at = NOW()
             WHERE rt.task_id = $taskId
               AND rp.user_id = $userId
               AND rp.status = 'active'
               AND rt.status <> 'completed'"
        );

        return true;
    }

    public static function startSobrietyTracking(int $userId): bool
    {
        if ($userId <= 0) return false;

        Database::iud(
            "UPDATE user_profiles
             SET sobriety_start_date = CURDATE(), updated_at = NOW()
             WHERE user_id = $userId
               AND sobriety_start_date IS NULL"
        );

        Database::iud(
            "INSERT INTO user_progress (user_id, date, days_sober, is_sober_today, notes)
             VALUES ($userId, CURDATE(), 0, 1, 'Started sobriety tracking')
             ON DUPLICATE KEY UPDATE
               is_sober_today = VALUES(is_sober_today),
               notes = VALUES(notes),
               updated_at = NOW()"
        );

        return true;
    }

    public static function resetSobrietyCounter(int $userId, string $reason = ''): bool
    {
        if ($userId <= 0) return false;
        $safeReason = addslashes($reason);

        Database::iud(
            "UPDATE user_profiles
             SET sobriety_start_date = CURDATE(), updated_at = NOW()
             WHERE user_id = $userId"
        );

        Database::iud(
            "INSERT INTO user_progress (user_id, date, days_sober, is_sober_today, notes)
             VALUES ($userId, CURDATE(), 0, 1, '$safeReason')
             ON DUPLICATE KEY UPDATE
               days_sober = 0,
               is_sober_today = 1,
               notes = VALUES(notes),
               updated_at = NOW()"
        );

        return true;
    }

    public static function getTasksByPlanId(int $planId, int $userId): array
    {
        $rs = Database::search(
            "SELECT rt.task_id, rt.title, rt.status, rt.priority, rt.task_type, rt.due_date
             FROM recovery_tasks rt
             INNER JOIN recovery_plans rp ON rp.plan_id = rt.plan_id
             WHERE rt.plan_id = $planId
               AND rp.user_id = $userId
             ORDER BY rt.sort_order ASC, rt.task_id ASC"
        );

        $tasks = [];
        while ($row = $rs->fetch_assoc()) {
            $tasks[] = [
                'taskId' => (int)$row['task_id'],
                'title' => $row['title'] ?? 'Task',
                'status' => $row['status'] ?? 'pending',
                'priority' => $row['priority'] ?? 'medium',
                'taskType' => $row['task_type'] ?? 'custom',
                'dueDate' => $row['due_date'] ?? null,
            ];
        }
        return $tasks;
    }

    public static function getGeneralPlans(): array
    {
        $rs = Database::search(
            "SELECT plan_id, title, description, category, plan_type, progress_percentage
             FROM recovery_plans
             WHERE is_template = 1
             ORDER BY updated_at DESC"
        );

        $plans = [];
        while ($row = $rs->fetch_assoc()) {
            $plans[] = [
                'planId' => (int)$row['plan_id'],
                'title' => $row['title'] ?? 'Recovery Plan',
                'description' => $row['description'] ?? '',
                'category' => $row['category'] ?? 'General',
                'planType' => $row['plan_type'] ?? 'self',
                'progressPercentage' => (int)($row['progress_percentage'] ?? 0),
            ];
        }
        return $plans;
    }

    public static function adoptGeneralPlan(int $templatePlanId, int $userId): bool
    {
        if ($templatePlanId <= 0 || $userId <= 0) return false;

        $templateRs = Database::search(
            "SELECT title, description, category, plan_type
             FROM recovery_plans
             WHERE plan_id = $templatePlanId
               AND is_template = 1
             LIMIT 1"
        );

        if (!$templateRs || $templateRs->num_rows === 0) {
            return false;
        }

        $tpl = $templateRs->fetch_assoc();
        $title = addslashes($tpl['title'] ?? 'Recovery Plan');
        $description = addslashes($tpl['description'] ?? '');
        $category = addslashes($tpl['category'] ?? '');
        $planType = addslashes($tpl['plan_type'] ?? 'self');

        Database::iud(
            "INSERT INTO recovery_plans
                (user_id, title, description, category, plan_type, status, start_date, progress_percentage, is_template, assigned_status, created_at, updated_at)
             VALUES
                ($userId, '$title', '$description', '$category', '$planType', 'active', CURDATE(), 0, 0, 'accepted', NOW(), NOW())"
        );

        $newPlanRs = Database::search("SELECT LAST_INSERT_ID() AS id");
        $newPlanRow = $newPlanRs->fetch_assoc();
        $newPlanId = (int)($newPlanRow['id'] ?? 0);
        if ($newPlanId <= 0) {
            return false;
        }

        Database::iud(
            "INSERT INTO recovery_goals (plan_id, goal_type, title, description, target_days, current_progress, status, created_at, updated_at)
             SELECT $newPlanId, goal_type, title, description, target_days, 0, 'in_progress', NOW(), NOW()
             FROM recovery_goals
             WHERE plan_id = $templatePlanId"
        );

        Database::iud(
            "INSERT INTO recovery_tasks (plan_id, title, description, task_type, status, priority, due_date, is_recurring, recurrence_pattern, sort_order, phase, created_at, updated_at)
             SELECT $newPlanId, title, description, task_type, 'pending', priority, due_date, is_recurring, recurrence_pattern, sort_order, phase, NOW(), NOW()
             FROM recovery_tasks
             WHERE plan_id = $templatePlanId"
        );

        return true;
    }
}
