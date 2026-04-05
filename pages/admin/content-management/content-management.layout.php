<?php
$pageTitle = 'Content Management';
require_once __DIR__ . '/../common/admin.html.head.php';
?>
<main class="admin-main-container">
    <?php require_once __DIR__ . '/../common/admin.sidebar.php'; ?>
    <section class="admin-main-content">
        <h1>Content Management</h1>
        <div class="admin-sub-container-1">
            <div class="admin-summary-card"><div class="admin-summary-card-content"><p class="admin-summary-card-title">Total Reports Today</p><p class="admin-summary-card-info"><?= $totalReportsToday ?></p></div></div>
            <div class="admin-summary-card"><div class="admin-summary-card-content"><p class="admin-summary-card-title">Pending Reports</p><p class="admin-summary-card-info"><?= $pendingReports ?></p></div></div>
            <div class="admin-summary-card"><div class="admin-summary-card-content"><p class="admin-summary-card-title">Actions This Week</p><p class="admin-summary-card-info"><?= $actionsThisWeek ?></p></div></div>
            <div class="admin-summary-card"><div class="admin-summary-card-content"><p class="admin-summary-card-title">Active Bans</p><p class="admin-summary-card-info"><?= $activeBans ?></p></div></div>
        </div>
        <div class="admin-sub-container-2">
            <form method="GET" class="content-management-filters"><div class="content-management-filters__dropdowns"><input type="text" name="type" placeholder="Type" value="<?= htmlspecialchars($filters['type'] === 'all' ? '' : $filters['type']) ?>"><input type="text" name="reason" placeholder="Reason" value="<?= htmlspecialchars($filters['reason'] === 'all' ? '' : $filters['reason']) ?>"><input type="text" name="status" placeholder="Status" value="<?= htmlspecialchars($filters['status'] === 'all' ? '' : $filters['status']) ?>"><button class="admin-button admin-button--secondary">Filter</button></div></form>
            <div class="admin-sub-container-2">
                <h2>Content Reports</h2>
                <table class="admin-table"><thead class="admin-table-header"><tr class="admin-table-row"><th class="admin-table-th">Content Preview</th><th class="admin-table-th">Author</th><th class="admin-table-th">Type</th><th class="admin-table-th">Reason</th><th class="admin-table-th">Reported By</th><th class="admin-table-th">Date</th><th class="admin-table-th">Status</th></tr></thead><tbody class="admin-table-body"><?php foreach ($reportedContent as $index => $item): ?><tr class="admin-table-row <?= $index % 2 === 0 ? 'admin-table-row--even' : 'admin-table-row--odd' ?>"><td class="admin-table-td"><?= htmlspecialchars($item['contentPreview']) ?></td><td class="admin-table-td"><?= htmlspecialchars($item['authorName']) ?></td><td class="admin-table-td"><?= htmlspecialchars($item['type']) ?></td><td class="admin-table-td"><?= htmlspecialchars($item['reason']) ?></td><td class="admin-table-td"><?= htmlspecialchars($item['reportedByName']) ?></td><td class="admin-table-td"><?= htmlspecialchars($item['date']) ?></td><td class="admin-table-td"><?= htmlspecialchars(ucfirst($item['status'])) ?></td></tr><?php endforeach; ?></tbody></table>
            </div>
        </div>
        <div class="admin-sub-container-1" style="align-items: stretch;"><div class="admin-data-card" style="flex:1;"><h3>Trending Posts</h3><p>Recovery milestone celebration</p><p>Weekly support group meeting</p><p>Healthy lifestyle tips</p></div><div class="admin-data-card" style="flex:1;"><h3>Moderation Summary</h3><p>Locked Threads: 5</p><p>Pinned Posts: 3</p><p>Active Warnings: 12</p></div></div>
    </section>
</main>

<?php require_once __DIR__ . '/../common/admin.footer.php'; ?>
</body>
</html>
