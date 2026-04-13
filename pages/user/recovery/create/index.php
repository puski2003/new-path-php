<?php
/**
 * /user/recovery/create — User creates their own recovery plan
 * GET  — show form
 * POST — validate, save, redirect
 */
require_once __DIR__ . '/../../common/user.head.php';
require_once __DIR__ . '/../recovery.model.php';

$userId = (int)$user['id'];

// Block if an active plan already exists
$activePlans = RecoveryModel::getUserActivePlans($userId);
if (!empty($activePlans)) {
    Response::redirect('/user/recovery?error=already_active');
}

// Stats for header card
$stats     = RecoveryModel::getProgressStats($userId);
$daysSober = (int)$stats['daysSober'];

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $shortGoal   = trim($_POST['short_goal'] ?? '');
    $longGoal    = trim($_POST['long_goal'] ?? '');

    if (strlen($title) < 3) {
        $error = 'Plan title must be at least 3 characters.';
    } else {
        Database::setUpConnection();
        $conn = Database::$connection;

        $safeTitle       = $conn->real_escape_string($title);
        $safeDescription = $conn->real_escape_string($description);

        Database::iud(
            "INSERT INTO recovery_plans
                (user_id, title, description, plan_type, status, start_date,
                 progress_percentage, is_template, assigned_status, created_at, updated_at)
             VALUES ($userId, '$safeTitle', '$safeDescription', 'self', 'active',
                     CURDATE(), 0, 0, NULL, NOW(), NOW())"
        );

        $planId = (int)$conn->insert_id;

        if ($planId > 0) {
            if ($shortGoal !== '') {
                $s = $conn->real_escape_string($shortGoal);
                Database::iud(
                    "INSERT INTO recovery_goals
                        (plan_id, goal_type, title, target_days, current_progress, status, created_at, updated_at)
                     VALUES ($planId, 'short_term', '$s', 30, 0, 'in_progress', NOW(), NOW())"
                );
            }
            if ($longGoal !== '') {
                $l = $conn->real_escape_string($longGoal);
                Database::iud(
                    "INSERT INTO recovery_goals
                        (plan_id, goal_type, title, target_days, current_progress, status, created_at, updated_at)
                     VALUES ($planId, 'long_term', '$l', 90, 0, 'in_progress', NOW(), NOW())"
                );
            }
        }

        Response::redirect('/user/recovery?planCreated=1');
    }
}

$pageTitle = 'Create Recovery Plan';
$pageStyle = ['user/recovery', 'user/checkin'];
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
                <h2>Create Recovery Plan</h2>
                <p>Set your own goals and build your path forward.</p>
            </div>
            <div class="card-container">
                <div class="card days-sober-card">
                    <div class="days-sober-content">
                        <p>DAYS SOBER</p>
                        <i data-lucide="heart" stroke-width="1" style="color:#335346"></i>
                    </div>
                    <h2><?= $daysSober ?></h2>
                </div>
            </div>
        </div>

        <div class="main-content-body">
            <div class="checkin-container">

                <div class="back-navigation">
                    <a href="/user/recovery" class="back-btn" title="Back">
                        <i data-lucide="chevron-left" style="width:18px;height:18px;"></i>
                    </a>
                </div>

                <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" class="checkin-form">

                    <div class="checkin-section">
                        <h4 class="checkin-section-title">
                            <i data-lucide="clipboard" style="width:16px;height:16px;"></i>
                            Plan Title
                        </h4>
                        <input type="text" name="title" class="form-input"
                               value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                               placeholder="e.g. My 30-Day Sobriety Plan" maxlength="200" required />
                    </div>

                    <div class="checkin-section">
                        <h4 class="checkin-section-title">
                            <i data-lucide="align-left" style="width:16px;height:16px;"></i>
                            Description
                            <span style="font-weight:400;color:var(--color-text-muted);font-size:var(--font-size-sm);">(optional)</span>
                        </h4>
                        <textarea name="description" class="checkin-notes" rows="3"
                                  placeholder="Describe your plan…" maxlength="1000"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>

                    <div class="checkin-section">
                        <h4 class="checkin-section-title">
                            <i data-lucide="target" style="width:16px;height:16px;"></i>
                            Short-term goal
                            <span style="font-weight:400;color:var(--color-text-muted);font-size:var(--font-size-sm);">(optional)</span>
                        </h4>
                        <input type="text" name="short_goal" class="form-input"
                               value="<?= htmlspecialchars($_POST['short_goal'] ?? '') ?>"
                               placeholder="e.g. Stay sober for 14 days" maxlength="300" />
                    </div>

                    <div class="checkin-section">
                        <h4 class="checkin-section-title">
                            <i data-lucide="flag" style="width:16px;height:16px;"></i>
                            Long-term goal
                            <span style="font-weight:400;color:var(--color-text-muted);font-size:var(--font-size-sm);">(optional)</span>
                        </h4>
                        <input type="text" name="long_goal" class="form-input"
                               value="<?= htmlspecialchars($_POST['long_goal'] ?? '') ?>"
                               placeholder="e.g. Maintain sobriety for 90 days" maxlength="300" />
                    </div>

                    <div style="display:flex;justify-content:flex-end;gap:var(--spacing-sm);padding-bottom:var(--spacing-2xl);">
                        <a href="/user/recovery" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="plus-circle" style="width:16px;height:16px;margin-right:4px;"></i>
                            Create Plan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </section>
</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>
</body>
</html>
