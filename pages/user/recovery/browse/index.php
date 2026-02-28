<?php
require_once __DIR__ . '/../../common/user.head.php';
require_once __DIR__ . '/../recovery.model.php';

$plans = RecoveryModel::getGeneralPlans();
$pageTitle = 'Browse Recovery Plans';
$pageStyle = ['user/dashboard', 'user/recovery', 'user/browse-plans'];
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
                <h2>Browse Recovery Plans</h2>
                <p>Select a plan template to start your journey.</p>
            </div>
            <div style="width: 25%"></div>
            <img src="/assets/img/recovery-head.svg" alt="Recovery" class="session-image" />
        </div>

        <div class="main-content-body">
            <div class="recovery-container">
                <div class="recovery-section">
                    <?php if (empty($plans)): ?>
                        <p>No template plans available right now.</p>
                    <?php else: ?>
                        <?php foreach ($plans as $plan): ?>
                            <div class="task-card" style="margin-bottom:12px;">
                                <div class="task-info">
                                    <div class="task-details">
                                        <span class="task-name"><?= htmlspecialchars($plan['title']) ?></span>
                                        <span class="task-status-text"><?= htmlspecialchars($plan['category']) ?> | <?= htmlspecialchars($plan['planType']) ?></span>
                                        <span class="task-status-text"><?= htmlspecialchars($plan['description']) ?></span>
                                    </div>
                                </div>
                                <form method="post" action="/user/recovery/adopt">
                                    <input type="hidden" name="planId" value="<?= (int)$plan['planId'] ?>" />
                                    <button type="submit" class="btn btn-primary">Adopt Plan</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <a href="/user/recovery" class="btn btn-secondary">Back</a>
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
