<?php
require_once __DIR__ . '/../../common/user.head.php';
require_once __DIR__ . '/../recovery.model.php';

$userId = (int)$user['id'];
$planId = (int)(Request::get('planId') ?? 0);
if ($planId <= 0) {
    Response::redirect('/user/recovery');
}

$plan = RecoveryModel::getPlanByIdForUser($planId, $userId);
if ($plan === null) {
    Response::status(404);
    require ROOT . '/pages/404.php';
    exit;
}

$goals = RecoveryModel::getGoalsByPlanId($planId);
$tasks = RecoveryModel::getTasksByPlanId($planId, $userId);

$pageTitle = 'View Recovery Plan';
$pageStyle = ['user/dashboard', 'user/recovery'];
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once __DIR__ . '/../../common/user.html.head.php'; ?>
<body>
<main class="main-container">
    <?php $activePage = 'recovery'; require_once __DIR__ . '/../../common/user.sidebar.php'; ?>
    <section class="main-content">
        <img src="/assets/img/main-content-head.svg" alt="Main Content Head background" class="main-header-bg-image" />
        <div class="main-content-header">
            <div class="main-content-header-text">
                <h2><?= htmlspecialchars($plan['title']) ?></h2>
                <p>Plan details, goals and tasks.</p>
            </div>
            <div style="width: 25%"></div>
            <img src="/assets/img/recovery-head.svg" alt="Recovery" class="session-image" />
        </div>

        <div class="main-content-body">
            <div class="recovery-container">
                <div class="recovery-section">
                    <div class="section-header">
                        <h3 class="section-title">Plan Overview</h3>
                        <div style="display:flex;gap:8px;">
                            <?php if (($plan['assignedStatus'] ?? '') === 'pending'): ?>
                                <form method="post" action="/user/recovery/accept">
                                    <input type="hidden" name="planId" value="<?= (int)$plan['planId'] ?>" />
                                    <button class="btn btn-primary" type="submit">Accept</button>
                                </form>
                                <form method="post" action="/user/recovery/reject">
                                    <input type="hidden" name="planId" value="<?= (int)$plan['planId'] ?>" />
                                    <button class="btn btn-secondary" type="submit">Reject</button>
                                </form>
                            <?php endif; ?>
                            <a href="/user/recovery" class="btn btn-secondary">Back</a>
                        </div>
                    </div>
                    <p><?= htmlspecialchars($plan['description']) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($plan['status']) ?> | <strong>Progress:</strong> <?= (int)$plan['progressPercentage'] ?>%</p>
                </div>

                <div class="recovery-grid">
                    <div class="recovery-left">
                        <div class="recovery-section">
                            <h3 class="section-title">Goals</h3>
                            <?php if (empty($goals)): ?>
                                <p>No goals found.</p>
                            <?php else: ?>
                                <?php foreach ($goals as $goal): ?>
                                    <div class="goal-item">
                                        <div class="goal-header">
                                            <span class="goal-title"><?= htmlspecialchars($goal['title']) ?></span>
                                            <span class="goal-days"><?= (int)$goal['currentProgress'] ?>/<?= (int)$goal['targetDays'] ?> Days</span>
                                        </div>
                                        <div class="progress-bar"><div class="progress-fill" style="width: <?= (int)$goal['progressPercentage'] ?>%"></div></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="recovery-right">
                        <div class="recovery-section">
                            <h3 class="section-title">Tasks</h3>
                            <?php if (empty($tasks)): ?>
                                <p>No tasks found.</p>
                            <?php else: ?>
                                <?php foreach ($tasks as $task): ?>
                                    <div class="task-card">
                                        <div class="task-info">
                                            <div class="task-icon <?= htmlspecialchars($task['status'] === 'completed' ? 'completed' : 'pending') ?>">
                                                <i data-lucide="<?= $task['status'] === 'completed' ? 'check-circle' : 'circle' ?>" stroke-width="2"></i>
                                            </div>
                                            <div class="task-details">
                                                <span class="task-name"><?= htmlspecialchars($task['title']) ?></span>
                                                <span class="task-status-text"><?= htmlspecialchars($task['taskType']) ?><?= !empty($task['dueDate']) ? ' | Due: ' . htmlspecialchars($task['dueDate']) : '' ?></span>
                                            </div>
                                        </div>
                                        <?php if ($task['status'] !== 'completed'): ?>
                                            <form method="post" action="/user/recovery/task/complete">
                                                <input type="hidden" name="taskId" value="<?= (int)$task['taskId'] ?>" />
                                                <button class="btn btn-primary btn-sm" type="submit">Complete</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>
<script src="/assets/js/auth/user-profile.js"></script>
</body>
</html>
