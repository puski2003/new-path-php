<?php
require_once __DIR__ . '/../../common/user.head.php';
require_once __DIR__ . '/../recovery.model.php';

$userId = (int)$user['id'];
$stats = RecoveryModel::getProgressStats($userId);

$pageTitle = 'Recovery Progress';
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
                <h2>Recovery Progress</h2>
                <p>Your recovery analytics and milestones.</p>
            </div>
            <div style="width: 25%"></div>
            <img src="/assets/img/recovery-head.svg" alt="Recovery" class="session-image" />
        </div>

        <div class="main-content-body">
            <div class="recovery-container">
                <div class="recovery-section">
                    <div class="section-header">
                        <h3 class="section-title">Current Stats</h3>
                        <a href="/user/recovery" class="btn btn-secondary">Back</a>
                    </div>
                    <div class="progress-stats">
                        <div class="stat-item"><span class="stat-value"><?= (int)$stats['daysSober'] ?></span><span class="stat-label">Days Sober</span></div>
                        <div class="stat-item"><span class="stat-value"><?= (int)$stats['totalDaysTracked'] ?></span><span class="stat-label">Total Days Tracked</span></div>
                        <div class="stat-item"><span class="stat-value"><?= (int)$stats['urgesLogged'] ?></span><span class="stat-label">Urges Logged</span></div>
                        <div class="stat-item"><span class="stat-value"><?= (int)$stats['sessionsCompleted'] ?></span><span class="stat-label">Sessions Completed</span></div>
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
