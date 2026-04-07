<?php
$pageTitle = 'Dashboard Overview';
$pageStyle = ['admin/dashboard'];
require_once __DIR__ . '/../common/admin.html.head.php';
?>
    <main class="admin-layout">

        <?php require_once __DIR__ . '/../common/admin.sidebar.php'; ?>

        <section class="admin-content">
            <div class="page-header">
                <h1>Dashboard Overview</h1>
                <span class="welcome-text">Welcome, <?= htmlspecialchars($user['name']) ?></span>
            </div>

            <div class="summary-cards">
                <div class="summary-card summary-card--growth">
                    <div class="summary-card__icon">
                        <!-- <img src="/assets/icons/user-plus.svg" alt=""> -->
                         <i data-lucide="user-plus"></i>
                    </div>
                    <div class="summary-card__body">
                        <p class="summary-card__label">Total Active Users</p>
                        <p class="summary-card__value"><?= number_format($data['totalUsers']) ?></p>
                    </div>
                </div>

                <div class="summary-card summary-card--pending">
                    <div class="summary-card__icon">
                        <!-- <img src="/assets/icons/clock.svg" alt=""> -->
                        <i data-lucide="clock"></i>
                    </div>
                    <div class="summary-card__body">
                        <p class="summary-card__label">Pending Applications</p>
                        <p class="summary-card__value"><?= $data['pendingApplications'] ?></p>
                        <p class="summary-card__sub">Counselor applications</p>
                    </div>
                </div>

                <div class="summary-card summary-card--upcoming">
                    <div class="summary-card__icon">
                        <!-- <img src="/assets/icons/calendar.svg" alt=""> -->
                        <i data-lucide="calendar"></i>
                    </div>
                    <div class="summary-card__body">
                        <p class="summary-card__label">Upcoming Sessions</p>
                        <p class="summary-card__value"><?= $data['upcomingSessions'] ?></p>
                        <p class="summary-card__sub">Next 24 hours</p>
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-card__icon">
                        <!-- <img src="/assets/icons/dollar-icon.svg" alt=""> -->
                        <i data-lucide="dollar-sign"></i>
                    </div>
                    <div class="summary-card__body">
                        <p class="summary-card__label">Revenue Today</p>
                        <p class="summary-card__value">$<?= number_format($data['revenueToday'], 2) ?></p>
                    </div>
                </div>
            </div>

            <div class="data-row">
                <div class="data-card data-card--wide">
                    <h2>User Growth Over Time</h2>
                    <p class="data-card__placeholder">Chart coming soon</p>
                </div>
                <div class="data-card">
                    <h2>Recovery Plan Adoption</h2>
                    <p class="data-card__placeholder">Chart coming soon</p>
                </div>
            </div>

            <div class="data-row">
                <div class="data-card data-card--grow">
                    <h2>Notifications &amp; Alerts</h2>
                    <div class="alert alert--urgent">
                        <strong>15 Unresolved Content Reports</strong>
                        <p>Requires immediate moderation</p>
                    </div>
                    <div class="alert alert--pending">
                        <strong>8 Counselor Applications Pending</strong>
                        <p>Review required for approval</p>
                    </div>
                    <div class="alert alert--warning">
                        <strong>3 Security Warnings</strong>
                        <p>Suspicious login attempts detected</p>
                    </div>
                    <div class="alert alert--info">
                        <strong>System Update Available</strong>
                        <p>New features ready to install</p>
                    </div>
                </div>

                <div class="data-card">
                    <h2>Quick Actions</h2>
                    <a href="/admin/user-management" class="btn btn--ghost btn--full">
                        <img src="/assets/icons/user-management-icon.svg" alt=""> User Management
                    </a>
                    <a href="/admin/content-management" class="btn btn--ghost btn--full">
                        <img src="/assets/icons/content-management-icon.svg" alt=""> Content Management
                    </a>
                    <a href="/admin/finances" class="btn btn--ghost btn--full">
                        <img src="/assets/icons/dollar-icon.svg" alt=""> Financials
                    </a>
                    <a href="/admin/analytics" class="btn btn--ghost btn--full">
                        <img src="/assets/icons/analytics-icon.svg" alt=""> Analytics
                    </a>
                </div>
            </div>

        </section>
    </main>

    <?php require_once __DIR__ . '/../common/admin.footer.php'; ?>
</body>

</html>
