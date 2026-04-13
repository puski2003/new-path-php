<?php
/**
 * /user/recovery/edit — User edits their self-created recovery plan
 * GET  ?id=X — show pre-filled form
 * POST        — validate, save, redirect
 */
require_once __DIR__ . '/../../common/user.head.php';
require_once __DIR__ . '/../recovery.model.php';

$userId = (int)$user['id'];
$planId = (int)($_GET['id'] ?? 0);

// Load plan and verify ownership — allow any plan the user owns with no counselor assigned
$plan = $planId > 0 ? RecoveryModel::getPlanByIdForUser($planId, $userId) : null;
if (!$plan) {
    Response::redirect('/user/recovery');
}
// Block if a counselor is directly managing this plan
$planCounselorRs = Database::search(
    "SELECT counselor_id FROM recovery_plans WHERE plan_id = $planId AND user_id = $userId LIMIT 1"
);
$planCounselorRow = $planCounselorRs ? $planCounselorRs->fetch_assoc() : null;
if (!$planCounselorRow || $planCounselorRow['counselor_id'] !== null) {
    Response::redirect('/user/recovery');
}

// Pre-fill goal fields from existing goals
$goals = RecoveryModel::getGoalsByPlanId($planId);
$shortGoalTitle = '';
$longGoalTitle  = '';
foreach ($goals as $g) {
    if ($g['goalType'] === 'short_term') $shortGoalTitle = $g['title'];
    if ($g['goalType'] === 'long_term')  $longGoalTitle  = $g['title'];
}

// Stats for header card
$stats     = RecoveryModel::getProgressStats($userId);
$daysSober = (int)$stats['daysSober'];

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = [
        'title'       => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'short_goal'  => trim($_POST['short_goal'] ?? ''),
        'long_goal'   => trim($_POST['long_goal'] ?? ''),
    ];
    if (strlen($input['title']) < 3) {
        $error = 'Plan title must be at least 3 characters.';
    } else {
        RecoveryModel::updateUserPlan($planId, $userId, $input);
        Response::redirect('/user/recovery?planUpdated=1');
    }
}

$pageTitle = 'Edit Recovery Plan';
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
                <h2>Edit Recovery Plan</h2>
                <p>Update your goals and plan description.</p>
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

                <form method="POST" action="/user/recovery/edit/?id=<?= $planId ?>" class="checkin-form">

                    <div class="checkin-section">
                        <h4 class="checkin-section-title">
                            <i data-lucide="clipboard" style="width:16px;height:16px;"></i>
                            Plan Title
                        </h4>
                        <input type="text" name="title" class="form-input"
                               value="<?= htmlspecialchars($_POST['title'] ?? $plan['title'] ?? '') ?>"
                               placeholder="e.g. My 30-Day Sobriety Plan" maxlength="200" required />
                    </div>

                    <div class="checkin-section">
                        <h4 class="checkin-section-title">
                            <i data-lucide="align-left" style="width:16px;height:16px;"></i>
                            Description
                            <span style="font-weight:400;color:var(--color-text-muted);font-size:var(--font-size-sm);">(optional)</span>
                        </h4>
                        <textarea name="description" class="checkin-notes" rows="3"
                                  placeholder="Describe your plan…" maxlength="1000"><?= htmlspecialchars($_POST['description'] ?? $plan['description'] ?? '') ?></textarea>
                    </div>

                    <div class="checkin-section">
                        <h4 class="checkin-section-title">
                            <i data-lucide="target" style="width:16px;height:16px;"></i>
                            Short-term goal
                            <span style="font-weight:400;color:var(--color-text-muted);font-size:var(--font-size-sm);">(optional)</span>
                        </h4>
                        <input type="text" name="short_goal" class="form-input"
                               value="<?= htmlspecialchars($_POST['short_goal'] ?? $shortGoalTitle) ?>"
                               placeholder="e.g. Stay sober for 14 days" maxlength="300" />
                    </div>

                    <div class="checkin-section">
                        <h4 class="checkin-section-title">
                            <i data-lucide="flag" style="width:16px;height:16px;"></i>
                            Long-term goal
                            <span style="font-weight:400;color:var(--color-text-muted);font-size:var(--font-size-sm);">(optional)</span>
                        </h4>
                        <input type="text" name="long_goal" class="form-input"
                               value="<?= htmlspecialchars($_POST['long_goal'] ?? $longGoalTitle) ?>"
                               placeholder="e.g. Maintain sobriety for 90 days" maxlength="300" />
                    </div>

                    <div style="display:flex;justify-content:flex-end;gap:var(--spacing-sm);padding-bottom:var(--spacing-2xl);">
                        <a href="/user/recovery" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save" style="width:16px;height:16px;margin-right:4px;"></i>
                            Save Changes
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
