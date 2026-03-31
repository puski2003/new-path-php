<?php
$pageTitle = 'Resources & Opportunities';
require_once __DIR__ . '/../common/admin.html.head.php';
?>
<main class="admin-main-container">
    <?php require_once __DIR__ . '/../common/admin.sidebar.php'; ?>
    <section class="admin-main-content">
        <h1>Resources &amp; Opportunities</h1>

        <div class="admin-tab-nav">
            <?php foreach (['job-ads' => 'Job Ads', 'skill-programs' => 'Skill Development Programs', 'help-centers' => 'Help Centers & Hotlines'] as $value => $label): ?>
                <a class="admin-tab-nav__item<?= $activeTab === $value ? ' admin-tab-nav__item--active' : '' ?>" href="/admin/resources?tab=<?= $value ?>"><?= htmlspecialchars($label) ?></a>
            <?php endforeach; ?>
        </div>

        <?php if ($activeTab === 'job-ads'): ?>
            <div class="admin-sub-container-2">
                <div class="admin-sub-container-1" style="justify-content: space-between; align-items: center;">
                    <h2>Job Opportunities</h2>
                    <a href="/admin/job-posts/add" class="admin-button admin-button--primary"><span class="button-text">Add Job Post</span></a>
                </div>
            </div>

            <div class="admin-sub-container-2">
                <form method="GET" action="/admin/resources" class="admin-filter-bar">
                    <input type="hidden" name="tab" value="job-ads">
                    <select name="status" class="admin-dropdown"><option value="all">All Status</option><option value="approved" <?= $filters['status'] === 'approved' ? 'selected' : '' ?>>Approved</option><option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option></select>
                    <input type="text" name="location" value="<?= htmlspecialchars($filters['location'] === 'all' ? '' : $filters['location']) ?>" placeholder="Location">
                    <select name="jobType" class="admin-dropdown"><option value="all">All Job Types</option><option value="Full-time" <?= $filters['jobType'] === 'Full-time' ? 'selected' : '' ?>>Full-time</option><option value="Part-time" <?= $filters['jobType'] === 'Part-time' ? 'selected' : '' ?>>Part-time</option><option value="Contract" <?= $filters['jobType'] === 'Contract' ? 'selected' : '' ?>>Contract</option></select>
                    <button type="submit" class="admin-button admin-button--secondary">Filter</button>
                </form>

                <table class="admin-table">
                    <thead class="admin-table-header"><tr class="admin-table-row"><th class="admin-table-th">Job Title</th><th class="admin-table-th">Company</th><th class="admin-table-th">Category</th><th class="admin-table-th">Location</th><th class="admin-table-th">Job Type</th><th class="admin-table-th">Salary</th><th class="admin-table-th">Status</th><th class="admin-table-th">Actions</th></tr></thead>
                    <tbody class="admin-table-body">
                    <?php if ($jobPosts === []): ?><tr class="admin-table-row"><td class="admin-table-td" colspan="8">No job posts found.</td></tr><?php endif; ?>
                    <?php foreach ($jobPosts as $index => $job): ?>
                        <tr class="admin-table-row <?= $index % 2 === 0 ? 'admin-table-row--even' : 'admin-table-row--odd' ?>">
                            <td class="admin-table-td"><?= htmlspecialchars($job['title']) ?></td>
                            <td class="admin-table-td"><?= htmlspecialchars($job['company']) ?></td>
                            <td class="admin-table-td"><?= htmlspecialchars($job['category']) ?></td>
                            <td class="admin-table-td"><?= htmlspecialchars($job['location']) ?></td>
                            <td class="admin-table-td"><?= htmlspecialchars($job['jobType']) ?></td>
                            <td class="admin-table-td"><?= $job['salaryRange'] !== '' ? htmlspecialchars($job['salaryRange']) : ($job['salary'] !== null ? '$' . number_format($job['salary'], 0) : 'Not specified') ?></td>
                            <td class="admin-table-td"><?= $job['active'] ? 'Active' : 'Inactive' ?></td>
                            <td class="admin-table-td admin-table-td--action">
                                <div class="admin-table-actions">
                                    <a href="/admin/job-posts/edit?id=<?= $job['jobId'] ?>" class="admin-button admin-button--ghost">Edit</a>
                                    <button type="button" class="admin-button admin-button--danger" onclick="deleteJobPost(<?= $job['jobId'] ?>, <?= json_encode($job['title']) ?>)">Delete</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($activeTab === 'skill-programs'): ?>
            <div class="admin-sub-container-2">
                <div class="admin-pending-submissions">
                    <div class="admin-pending-submissions__header"><h2>Pending Program Submissions</h2></div>
                    <div class="admin-pending-submissions__list">
                        <?php foreach ($pendingPrograms as $submission): ?>
                            <div class="admin-pending-submission-card">
                                <div class="admin-pending-submission-card__content">
                                    <h3 class="admin-pending-submission-card__title"><?= htmlspecialchars($submission['programName']) ?> - <?= htmlspecialchars($submission['providerName']) ?></h3>
                                    <p class="admin-pending-submission-card__subtitle"><?= htmlspecialchars($submission['format']) ?> • <?= htmlspecialchars($submission['duration']) ?> • Submitted <?= htmlspecialchars($submission['submittedTime']) ?></p>
                                </div>
                                <div class="admin-pending-submission-card__actions">
                                    <button type="button" class="admin-button admin-button--success">Approve</button>
                                    <button type="button" class="admin-button admin-button--danger">Reject</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="admin-sub-container-2">
                <table class="admin-table">
                    <thead class="admin-table-header"><tr class="admin-table-row"><th class="admin-table-th">Program</th><th class="admin-table-th">Provider</th><th class="admin-table-th">Category</th><th class="admin-table-th">Duration</th><th class="admin-table-th">Format</th><th class="admin-table-th">Cost</th><th class="admin-table-th">Status</th></tr></thead>
                    <tbody class="admin-table-body">
                    <?php foreach ($programs as $index => $program): ?>
                        <tr class="admin-table-row <?= $index % 2 === 0 ? 'admin-table-row--even' : 'admin-table-row--odd' ?>">
                            <td class="admin-table-td"><?= htmlspecialchars($program['programName']) ?></td>
                            <td class="admin-table-td"><?= htmlspecialchars($program['provider']) ?></td>
                            <td class="admin-table-td"><?= htmlspecialchars($program['category']) ?></td>
                            <td class="admin-table-td"><?= htmlspecialchars($program['duration']) ?></td>
                            <td class="admin-table-td"><?= htmlspecialchars($program['format']) ?></td>
                            <td class="admin-table-td"><?= htmlspecialchars($program['cost']) ?></td>
                            <td class="admin-table-td"><?= htmlspecialchars(ucfirst($program['status'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="admin-sub-container-2">
                <div class="admin-sub-container-1" style="justify-content: space-between; align-items: center;">
                    <h2>Help Centers &amp; Hotlines</h2>
                    <a href="/admin/help-center/add" class="admin-button admin-button--primary"><span class="button-text">Add Help Center</span></a>
                </div>
            </div>
            <div class="admin-sub-container-2">
                <form method="GET" action="/admin/resources" class="admin-filter-bar">
                    <input type="hidden" name="tab" value="help-centers">
                    <select name="centerStatus" class="admin-dropdown"><option value="all">All Status</option><option value="active" <?= $filters['centerStatus'] === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= $filters['centerStatus'] === 'inactive' ? 'selected' : '' ?>>Inactive</option></select>
                    <input type="text" name="type" value="<?= htmlspecialchars($filters['type'] === 'all' ? '' : $filters['type']) ?>" placeholder="Type">
                    <input type="text" name="centerCategory" value="<?= htmlspecialchars($filters['centerCategory'] === 'all' ? '' : $filters['centerCategory']) ?>" placeholder="Category">
                    <button type="submit" class="admin-button admin-button--secondary">Filter</button>
                </form>
                <table class="admin-table">
                    <thead class="admin-table-header"><tr class="admin-table-row"><th class="admin-table-th">Name</th><th class="admin-table-th">Organization</th><th class="admin-table-th">Type</th><th class="admin-table-th">Category</th><th class="admin-table-th">Phone Number</th><th class="admin-table-th">Availability</th><th class="admin-table-th">Status</th><th class="admin-table-th">Actions</th></tr></thead>
                    <tbody class="admin-table-body">
                    <?php if ($helpCenters === []): ?><tr class="admin-table-row"><td class="admin-table-td" colspan="8">No help centers found.</td></tr><?php endif; ?>
                    <?php foreach ($helpCenters as $index => $center): ?>
                        <tr class="admin-table-row <?= $index % 2 === 0 ? 'admin-table-row--even' : 'admin-table-row--odd' ?>">
                            <td class="admin-table-td"><?= htmlspecialchars($center['name']) ?></td>
                            <td class="admin-table-td"><?= htmlspecialchars($center['organization']) ?></td>
                            <td class="admin-table-td"><?= htmlspecialchars($center['type']) ?></td>
                            <td class="admin-table-td"><?= htmlspecialchars($center['category']) ?></td>
                            <td class="admin-table-td"><?= htmlspecialchars($center['phoneNumber']) ?></td>
                            <td class="admin-table-td"><?= htmlspecialchars($center['availability']) ?></td>
                            <td class="admin-table-td"><?= $center['active'] ? 'Active' : 'Inactive' ?></td>
                            <td class="admin-table-td admin-table-td--action">
                                <div class="admin-table-actions">
                                    <a href="/admin/help-center/edit?id=<?= $center['helpCenterId'] ?>" class="admin-button admin-button--ghost">Edit</a>
                                    <button type="button" class="admin-button admin-button--danger" onclick="deleteHelpCenter(<?= $center['helpCenterId'] ?>, <?= json_encode($center['name']) ?>)">Delete</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>
<script>
function deleteJobPost(jobId, title) {
    if (!confirm('Delete "' + title + '"?')) return;
    fetch('/admin/job-posts/delete', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({jobId})
    }).then(() => window.location.reload());
}
function deleteHelpCenter(helpCenterId, title) {
    if (!confirm('Delete "' + title + '"?')) return;
    fetch('/admin/help-center/delete', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({helpCenterId})
    }).then(() => window.location.reload());
}
</script>
</body>
</html>
