<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — New Path</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/components/sidebar.css">
    <link rel="stylesheet" href="/assets/css/components/table.css">
    <link rel="stylesheet" href="/assets/css/components/button.css">
    <link rel="stylesheet" href="/assets/css/components/summary-card.css">
    <link rel="stylesheet" href="/assets/css/admin/dashboard.css">
</head>

<body>
    <main class="admin-layout">

        <?php require_once ROOT . '/pages/admin/common/admin.sidebar.php'; ?>

        <section class="admin-content">
            <div class="page-header">
                <h1>Dashboard Overview</h1>
                <span class="welcome-text">Welcome, <?= htmlspecialchars($user['name']) ?></span>
            </div>

            <!-- Summary Cards -->
            <div class="summary-cards">
                <div class="summary-card summary-card--growth">
                    <div class="summary-card__icon">
                        <img src="/assets/icons/user-plus.svg" alt="">
                    </div>
                    <div class="summary-card__body">
                        <p class="summary-card__label">Total Active Users</p>
                        <p class="summary-card__value"><?= number_format($data['totalUsers']) ?></p>
                    </div>
                </div>

                <div class="summary-card summary-card--pending">
                    <div class="summary-card__icon">
                        <img src="/assets/icons/clock.svg" alt="">
                    </div>
                    <div class="summary-card__body">
                        <p class="summary-card__label">Pending Applications</p>
                        <p class="summary-card__value"><?= $data['pendingApplications'] ?></p>
                        <p class="summary-card__sub">Counselor applications</p>
                    </div>
                </div>

                <div class="summary-card summary-card--upcoming">
                    <div class="summary-card__icon">
                        <img src="/assets/icons/calendar.svg" alt="">
                    </div>
                    <div class="summary-card__body">
                        <p class="summary-card__label">Upcoming Sessions</p>
                        <p class="summary-card__value"><?= $data['upcomingSessions'] ?></p>
                        <p class="summary-card__sub">Next 24 hours</p>
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-card__icon">
                        <img src="/assets/icons/dollar-icon.svg" alt="">
                    </div>
                    <div class="summary-card__body">
                        <p class="summary-card__label">Revenue Today</p>
                        <p class="summary-card__value">$<?= number_format($data['revenueToday'], 2) ?></p>
                    </div>
                </div>
            </div>

            <!-- Charts Row (placeholder for future charting library) -->
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

            <!-- Bottom Row -->
            <div class="data-row">
                <!-- Alerts -->
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

                <!-- Quick Actions -->
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

    <script src="/assets/js/components/sidebar.js" defer></script>
</body>

</html>