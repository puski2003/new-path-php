<?php
$pageTitle = 'Analytics and Reports';
require_once __DIR__ . '/../common/admin.html.head.php';
?>
<main class="admin-main-container">
    <?php require_once __DIR__ . '/../common/admin.sidebar.php'; ?>
    <section class="admin-main-content">
        <h1>Analytics and Reports</h1>
        <div class="admin-sub-container-1" style="justify-content: space-between; align-items: center;">
            <form method="GET"><select name="timePeriod" class="admin-dropdown" onchange="this.form.submit()"><option value="lastWeek" <?= $timePeriod === 'lastWeek' ? 'selected' : '' ?>>Last Week</option><option value="lastMonth" <?= $timePeriod === 'lastMonth' ? 'selected' : '' ?>>Last Month</option><option value="lastYear" <?= $timePeriod === 'lastYear' ? 'selected' : '' ?>>Last Year</option></select></form>
            <div class="admin-sub-container-1"><button class="admin-button admin-button--secondary">Export CSV</button><button class="admin-button admin-button--secondary">Export PDF</button></div>
        </div>
        <div class="admin-sub-container-1">
            <div class="admin-summary-card"><div class="admin-summary-card-content"><p class="admin-summary-card-title">Users</p><p class="admin-summary-card-info"><?= $summary['totalUsers'] ?></p><p class="admin-summary-card-subinfo">Registered users</p></div></div>
            <div class="admin-summary-card"><div class="admin-summary-card-content"><p class="admin-summary-card-title">Sessions</p><p class="admin-summary-card-info"><?= $summary['totalSessions'] ?></p><p class="admin-summary-card-subinfo">All scheduled sessions</p></div></div>
            <div class="admin-summary-card"><div class="admin-summary-card-content"><p class="admin-summary-card-title">Recovery Plans</p><p class="admin-summary-card-info"><?= $summary['totalPlans'] ?></p><p class="admin-summary-card-subinfo">Counselor and self guided</p></div></div>
            <div class="admin-summary-card"><div class="admin-summary-card-content"><p class="admin-summary-card-title">Avg Plan Progress</p><p class="admin-summary-card-info"><?= number_format($summary['avgPlanProgress'], 1) ?>%</p><p class="admin-summary-card-subinfo">Across all plans</p></div></div>
        </div>
        <div class="admin-sub-container-1">
            <div class="admin-data-card" style="height: 320px;"><h3>User Engagement Analytics</h3><p class="data-card__placeholder">Chart placeholder</p></div>
            <div class="admin-data-card" style="height: 320px;"><h3>Recovery Plan Adoption</h3><p class="data-card__placeholder">Chart placeholder</p></div>
        </div>
        <div class="admin-sub-container-2"><div class="admin-data-card" style="height: 240px;"><h3>Post Recovery Engagement</h3><p>Active job posts: <?= $summary['activeJobs'] ?></p></div></div>
    </section>
</main>
</body>
</html>
