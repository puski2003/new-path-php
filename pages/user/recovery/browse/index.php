<?php
require_once __DIR__ . '/../../common/user.head.php';
require_once __DIR__ . '/../recovery.model.php';

$plans     = RecoveryModel::getGeneralPlans();
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
                <a href="/user/recovery" class="back-btn" aria-label="Back">
                    <i data-lucide="arrow-left" stroke-width="2" class="back-icon"></i>
                </a>
                <h2>Browse Recovery Plans</h2>
            </div>
            <p class="page-subtitle">Choose how you'd like to start your recovery journey.</p>
        </div>

        <div class="main-content-body">
            <div style="max-width:780px;margin:0 auto;padding:var(--spacing-xl);">

                <?php if (isset($_GET['error']) && $_GET['error'] === 'already_active'): ?>
                <div class="error-message" style="margin-bottom:var(--spacing-lg);">
                    You already have an active recovery plan. Complete or cancel it before adopting a new one.
                </div>
                <?php endif; ?>

                <!-- Choice cards -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--spacing-xl);margin-bottom:var(--spacing-2xl);">

                    <!-- Option A: Counselor plan -->
                    <div class="plan-card" style="border:2px solid var(--color-border-primary);cursor:default;">
                        <div class="plan-icon">
                            <i data-lucide="user-check" style="width:24px;height:24px;"></i>
                        </div>
                        <div>
                            <h3 style="font-size:var(--font-size-base);font-weight:var(--font-weight-semibold);margin-bottom:6px;">
                                Request from Counselor
                            </h3>
                            <p class="plan-description" style="-webkit-line-clamp:unset;">
                                Work with your assigned counselor to build a personalised recovery plan tailored specifically to your needs and goals.
                            </p>
                        </div>
                        <a href="/user/counselors" class="btn btn-primary" style="text-align:center;">
                            Find a Counselor
                        </a>
                    </div>

                    <!-- Option B: System plan -->
                    <div class="plan-card" style="border:2px solid var(--color-primary);cursor:default;">
                        <div class="plan-icon" style="background:var(--color-primary);color:#fff;">
                            <i data-lucide="layout-template" style="width:24px;height:24px;"></i>
                        </div>
                        <div>
                            <h3 style="font-size:var(--font-size-base);font-weight:var(--font-weight-semibold);margin-bottom:6px;">
                                Use a System Plan
                            </h3>
                            <p class="plan-description" style="-webkit-line-clamp:unset;">
                                Choose from our library of evidence-based recovery plan templates. Get started immediately with a structured programme.
                            </p>
                        </div>
                        <a href="#system-plans" class="btn btn-primary" style="text-align:center;">
                            Browse Templates
                        </a>
                    </div>

                </div>

                <!-- System plan templates -->
                <div id="system-plans">
                    <h3 style="font-size:var(--font-size-base);font-weight:var(--font-weight-semibold);color:var(--color-text-primary);margin-bottom:var(--spacing-md);display:flex;align-items:center;gap:8px;">
                        <i data-lucide="layout-template" style="width:16px;height:16px;color:var(--color-primary);"></i>
                        System Plan Templates
                    </h3>

                    <?php if (empty($plans)): ?>
                    <div class="empty-state">
                        <i data-lucide="inbox" style="width:40px;height:40px;color:var(--color-text-muted);display:block;margin:0 auto var(--spacing-md);"></i>
                        <h3>No templates yet</h3>
                        <p>System plan templates haven't been added yet. Request a plan from your counselor above.</p>
                    </div>
                    <?php else: ?>
                    <div class="plans-grid">
                        <?php foreach ($plans as $plan): ?>
                        <div class="plan-card">
                            <div class="plan-icon">
                                <i data-lucide="clipboard-list" style="width:24px;height:24px;"></i>
                            </div>
                            <div>
                                <h4 class="plan-title"><?= htmlspecialchars($plan['title']) ?></h4>
                                <div class="plan-meta" style="margin:6px 0;">
                                    <span class="plan-category"><?= htmlspecialchars($plan['category']) ?></span>
                                    <span class="plan-category"><?= htmlspecialchars($plan['planType']) ?></span>
                                </div>
                                <p class="plan-description"><?= htmlspecialchars($plan['description']) ?></p>
                            </div>
                            <form method="post" action="/user/recovery/adopt">
                                <input type="hidden" name="planId" value="<?= (int)$plan['planId'] ?>" />
                                <button type="submit" class="btn btn-primary" style="width:100%;">Adopt Plan</button>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>
</main>
<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>
</body>
</html>
