<!DOCTYPE html>
<html lang="en">
<?php require_once __DIR__ . '/../common/user.html.head.php'; ?>

<body>
    <main class="main-container">
        <?php
        $activePage = 'recovery';
        require_once __DIR__ . '/../common/user.sidebar.php';
        ?>

        <section class="main-content">
            <img
                src="/assets/img/main-content-head.svg"
                alt="Main Content Head background"
                class="main-header-bg-image" />

            <div class="main-content-header">
                <div class="main-content-header-text">
                    <h2>Recovery Plan</h2>
                    <p>Your scheduled guidance, all in one place.</p>
                </div>

                <div style="width: 25%"></div>
                <img
                    src="/assets/img/recovery-head.svg"
                    alt="Session"
                    class="session-image" />
            </div>

            <div class="main-content-body">
                <div class="recovery-container">
                    <?php require __DIR__ . '/../common/user.recovery-header.php'; ?>

                    <div class="recovery-grid">
                        <div class="recovery-left">
                            <?php require __DIR__ . '/../common/user.daily-tasks.php'; ?>
                            <?php require __DIR__ . '/../common/user.goals-rewards.php'; ?>
                            <?php require __DIR__ . '/../common/user.counselor-support.php'; ?>
                        </div>

                        <div class="recovery-right">
                            <?php require __DIR__ . '/../common/user.progress-tracker.php'; ?>
                            <?php require __DIR__ . '/../common/user.daily-reflection.php'; ?>
                            <?php require __DIR__ . '/../common/user.coping-tools.php'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
    <script src="/assets/js/user/recovery.js"></script>
    <script src="/assets/js/auth/user-profile.js"></script>
</body>

</html>
