<?php
require_once __DIR__ . '/../../common/user.head.php';
require_once __DIR__ . '/../recovery.model.php';

$plans = RecoveryModel::getSystemPlans();
$pageTitle = 'Browse Recovery Plans';
$pageStyle = ['user/dashboard', 'user/manage-plans', 'user/browse-plans'];
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once __DIR__ . '/../../common/user.html.head.php'; ?>
<body>
<main class="main-container">
    <?php $activePage = 'recovery'; require_once __DIR__ . '/../../common/user.sidebar.php'; ?>
    <section class="main-content">
        <img src="/assets/img/main-content-head.svg" alt="" class="main-header-bg-image" />

        <div class="main-content-header">
            <div class="main-content-header-text">
                <a href="/user/recovery/manage" class="back-btn" aria-label="Back">
                    <i data-lucide="arrow-left" stroke-width="2" class="back-icon"></i>
                </a>
                <h2>Browse Recovery Plans</h2>
            </div>
            <p class="page-subtitle">Choose an admin-created plan to start your recovery journey.</p>
        </div>

        <div class="main-content-body">
            <div style="max-width:860px;margin:0 auto;padding:var(--spacing-xl);">

                <?php if (isset($_GET['error']) && $_GET['error'] === 'already_active'): ?>
                <div class="error-message" style="margin-bottom:var(--spacing-lg);">
                    You already have an active recovery plan. Complete or pause it before adopting a new one.
                </div>
                <?php endif; ?>

                <?php if (empty($plans)): ?>
                <div class="empty-state">
                    <i data-lucide="inbox" style="width:40px;height:40px;color:var(--color-text-muted);display:block;margin:0 auto var(--spacing-md);"></i>
                    <h3>No plans available yet</h3>
                    <p>System plans haven't been added by an admin yet. Check back soon or .

                    </p>
                    <div>
                        <a href="/user/counselors">find a counselor</a>
                    </div>
                </div>
                <?php else: ?>
                <div class="plans-grid">
                    <?php foreach ($plans as $plan): ?>
                    <div class="plan-card" style="padding:0;overflow:hidden;">
                        <?php if (!empty($plan['image'])): ?>
                        <img src="<?= htmlspecialchars($plan['image']) ?>" alt="<?= htmlspecialchars($plan['title']) ?>"
                             style="width:100%;height:160px;object-fit:cover;display:block;" />
                        <?php else: ?>
                        <div style="width:100%;height:120px;background:linear-gradient(135deg,var(--color-primary-light,#e8f5e9),var(--color-primary,#2e7d32));display:flex;align-items:center;justify-content:center;">
                            <i data-lucide="clipboard-list" style="width:36px;height:36px;color:#fff;opacity:.8;"></i>
                        </div>
                        <?php endif; ?>
                        <div style="padding:var(--spacing-md);">
                            <h4 class="plan-title"><?= htmlspecialchars($plan['title']) ?></h4>
                            <?php if (!empty($plan['category'])): ?>
                            <div class="plan-meta" style="margin:4px 0 8px;">
                                <span class="plan-category"><?= htmlspecialchars($plan['category']) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($plan['description'])): ?>
                            <p class="plan-description"><?= htmlspecialchars($plan['description']) ?></p>
                            <?php endif; ?>
                            <form method="post" action="/user/recovery/adopt" style="margin-top:var(--spacing-md);">
                                <input type="hidden" name="planId" value="<?= (int)$plan['planId'] ?>" />
                                <button type="submit" class="btn btn-primary" style="width:100%;">Adopt Plan</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </section>
</main>
<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>
</body>
</html>
