<?php
$pageTitle = 'Recovery Plans & Templates';
require_once __DIR__ . '/../common/admin.html.head.php';
?>
<main class="admin-main-container">
    <?php require_once __DIR__ . '/../common/admin.sidebar.php'; ?>
    <section class="admin-main-content">
        <h1>Recovery Plans &amp; Templates</h1>
        <div class="admin-tab-nav">
            <a class="admin-tab-nav__item<?= $activeTab === 'pre-built' ? ' admin-tab-nav__item--active' : '' ?>" href="/admin/recovery-plans?tab=pre-built">Pre-built Recovery Plans</a>
            <a class="admin-tab-nav__item<?= $activeTab === 'onboarding' ? ' admin-tab-nav__item--active' : '' ?>" href="/admin/recovery-plans?tab=onboarding">Onboarding Questionnaires</a>
        </div>
        <?php if ($activeTab === 'pre-built'): ?>
            <div class="admin-sub-container-2">
                <div class="admin-page-header-row">
                    <form method="GET" class="recovery-plans-actions"><input type="hidden" name="tab" value="pre-built"><input type="text" name="search" placeholder="Search plans..." value="<?= htmlspecialchars($filters['search']) ?>"><input type="text" name="category" placeholder="Category" value="<?= htmlspecialchars($filters['category'] === 'all' ? '' : $filters['category']) ?>"><button class="admin-button admin-button--secondary">Filter</button></form>
                    <a href="/admin/recovery-plans/create" class="admin-button admin-button--primary">+ Create Plan</a>
                </div>
                <table class="admin-table"><thead class="admin-table-header"><tr class="admin-table-row"><th class="admin-table-th">Plan Name</th><th class="admin-table-th">Category</th><th class="admin-table-th">Adoption Rate</th><th class="admin-table-th">Created By</th><th class="admin-table-th">Last Updated</th></tr></thead><tbody class="admin-table-body"><?php foreach ($plans as $index => $plan): ?><tr class="admin-table-row <?= $index % 2 === 0 ? 'admin-table-row--even' : 'admin-table-row--odd' ?>"><td class="admin-table-td"><strong><?= htmlspecialchars($plan['planName']) ?></strong><br><small><?= htmlspecialchars($plan['description']) ?></small></td><td class="admin-table-td"><?= htmlspecialchars($plan['category']) ?></td><td class="admin-table-td"><?= $plan['adoptionRate'] ?>%</td><td class="admin-table-td"><?= htmlspecialchars($plan['createdBy']) ?></td><td class="admin-table-td"><?= htmlspecialchars($plan['lastUpdated']) ?></td></tr><?php endforeach; ?></tbody></table>

                <?php
                $pagination = $plansPagination;
                $basePath = '/admin/recovery-plans';
                $query = array_merge($filters, ['tab' => $activeTab]);
                require __DIR__ . '/../common/admin.pagination.php';
                ?>
            </div>
        <?php else: ?>
            <div class="admin-sub-container-2">
                <table class="admin-table"><thead class="admin-table-header"><tr class="admin-table-row"><th class="admin-table-th">Question</th><th class="admin-table-th">Question Type</th><th class="admin-table-th">Rating</th><th class="admin-table-th">Status</th><th class="admin-table-th">Created On</th></tr></thead><tbody class="admin-table-body"><?php foreach ($questions as $index => $question): ?><tr class="admin-table-row <?= $index % 2 === 0 ? 'admin-table-row--even' : 'admin-table-row--odd' ?>"><td class="admin-table-td"><?= htmlspecialchars($question['question']) ?></td><td class="admin-table-td"><?= htmlspecialchars($question['questionType']) ?></td><td class="admin-table-td"><?= htmlspecialchars((string) $question['rating']) ?></td><td class="admin-table-td"><?= htmlspecialchars($question['status']) ?></td><td class="admin-table-td"><?= htmlspecialchars($question['createdOn']) ?></td></tr><?php endforeach; ?></tbody></table>

                <?php
                $pagination = $questionsPagination;
                $basePath = '/admin/recovery-plans';
                $query = array_merge($filters, ['tab' => $activeTab]);
                require __DIR__ . '/../common/admin.pagination.php';
                ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/../common/admin.footer.php'; ?>
</body>
</html>
