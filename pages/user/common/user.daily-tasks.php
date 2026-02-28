<div class="recovery-section daily-tasks">
    <div class="section-header">
        <h3 class="section-title">Daily Tasks</h3>
        <span class="tasks-progress"><?= $completedCount ?>/<?= ($completedCount + $pendingCount) ?> completed</span>
    </div>

    <div class="tasks-list">
        <?php if (empty($tasks)): ?>
            <div class="empty-tasks">
                <p>No tasks yet. Browse recovery plans to get started!</p>
            </div>
        <?php else: ?>
            <?php foreach ($tasks as $task): ?>
                <div class="task-card">
                    <div class="task-info">
                        <?php if (($task['status'] ?? '') === 'completed'): ?>
                            <div class="task-icon completed"><i data-lucide="check-circle" stroke-width="2"></i></div>
                        <?php elseif (($task['priority'] ?? '') === 'high'): ?>
                            <div class="task-icon urgent"><i data-lucide="alert-circle" stroke-width="2"></i></div>
                        <?php else: ?>
                            <div class="task-icon pending"><i data-lucide="circle" stroke-width="2"></i></div>
                        <?php endif; ?>

                        <div class="task-details">
                            <span class="task-name"><?= htmlspecialchars($task['title']) ?></span>
                            <span class="task-status-text">
                                <?php if (($task['status'] ?? '') === 'completed'): ?>
                                    Completed
                                <?php elseif (!empty($task['dueDate'])): ?>
                                    Due: <?= htmlspecialchars($task['dueDate']) ?>
                                <?php else: ?>
                                    <?= htmlspecialchars($task['taskType'] ?? 'Task') ?>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    <?php if (($task['status'] ?? '') !== 'completed'): ?>
                        <form action="/user/recovery/task/complete" method="post">
                            <input type="hidden" name="taskId" value="<?= (int)$task['taskId'] ?>" />
                            <button type="submit" class="btn btn-primary btn-sm">Complete</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
