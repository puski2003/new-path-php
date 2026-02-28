<?php if (!empty($pendingPlans)): ?>
    <?php foreach ($pendingPlans as $pendingPlan): ?>
        <div class="recovery-header">
            <div class="recovery-status">
                <span class="status-text">Your counselor shared a new plan: "<?= htmlspecialchars($pendingPlan['title']) ?>"</span>
                <form action="/user/recovery/accept" method="post" style="display: inline">
                    <input type="hidden" name="planId" value="<?= (int)$pendingPlan['planId'] ?>" />
                    <button type="submit" class="btn btn-primary review-btn">Review &amp; Accept</button>
                </form>
            </div>
            <a href="/user/recovery/view?planId=<?= (int)$pendingPlan['planId'] ?>" class="btn btn-secondary create-plan-btn">
                <i data-lucide="eye" stroke-width="1" class="btn-icon"></i>
                View Recovery Plan
            </a>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (empty($pendingPlans) && empty($activePlans)): ?>
    <div class="recovery-header">
        <div class="recovery-status">
            <span class="status-text">No active recovery plan. Browse available plans to get started!</span>
        </div>
        <a href="/user/recovery/browse" class="btn btn-primary create-plan-btn">
            <i data-lucide="clipboard-check" stroke-width="1" class="btn-icon"></i>
            Browse Recovery Plans
        </a>
    </div>
<?php endif; ?>

<?php if (!empty($activePlans)): ?>
    <div class="recovery-header">
        <div class="recovery-status">
            <span class="status-text">Active Plan: <?= htmlspecialchars($activePlans[0]['title']) ?></span>
        </div>
        <a href="/user/recovery/manage" class="btn btn-primary create-plan-btn">
            <i data-lucide="clipboard-check" stroke-width="1" class="btn-icon"></i>
            Manage Recovery Plan
        </a>
    </div>
<?php endif; ?>
