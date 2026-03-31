<?php
$pageTitle = 'Edit Job Post';
require_once __DIR__ . '/../../common/admin.html.head.php';
?>
<main class="admin-main-container">
    <?php require_once __DIR__ . '/../../common/admin.sidebar.php'; ?>
    <section class="admin-main-content">
        <div class="admin-sub-container-1" style="justify-content: space-between; align-items: center;">
            <h1>Edit Job Post</h1>
            <a href="/admin/resources?tab=job-ads" class="admin-button admin-button--secondary"><span class="button-text">Back to Job Ads</span></a>
        </div>
        <div class="admin-sub-container-2">
            <?php if ($error !== ''): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <?php if ($success !== ''): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
            <?php if ($jobPost): ?>
                <form method="POST" class="admin-form" style="max-width: 800px;">
                    <input type="hidden" name="jobId" value="<?= $jobPost['jobId'] ?>">
                    <div class="form-group"><label class="form-label" for="title">Job Title *</label><input class="form-input" id="title" name="title" value="<?= htmlspecialchars($jobPost['title']) ?>"></div>
                    <div class="form-group"><label class="form-label" for="company">Company Name *</label><input class="form-input" id="company" name="company" value="<?= htmlspecialchars($jobPost['company']) ?>"></div>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label" for="category">Category *</label><input class="form-input" id="category" name="category" value="<?= htmlspecialchars($jobPost['category']) ?>"></div>
                        <div class="form-group"><label class="form-label" for="jobType">Job Type *</label><select class="form-select" id="jobType" name="jobType"><?php foreach (['Full-time','Part-time','Contract','Temporary','Internship'] as $jobType): ?><option value="<?= $jobType ?>" <?= $jobPost['jobType'] === $jobType ? 'selected' : '' ?>><?= $jobType ?></option><?php endforeach; ?></select></div>
                    </div>
                    <div class="form-group"><label class="form-label" for="location">Location *</label><input class="form-input" id="location" name="location" value="<?= htmlspecialchars($jobPost['location']) ?>"></div>
                    <div class="form-group"><label class="form-label" for="description">Description *</label><textarea class="form-textarea" id="description" name="description" rows="6"><?= htmlspecialchars($jobPost['description']) ?></textarea></div>
                    <div class="form-group"><label class="form-label" for="requirements">Requirements</label><textarea class="form-textarea" id="requirements" name="requirements" rows="4"><?= htmlspecialchars($jobPost['requirements']) ?></textarea></div>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label" for="salaryRange">Salary Range</label><input class="form-input" id="salaryRange" name="salaryRange" value="<?= htmlspecialchars($jobPost['salaryRange']) ?>"></div>
                        <div class="form-group"><label class="form-label" for="salary">Salary</label><input class="form-input" id="salary" name="salary" type="number" step="0.01" value="<?= htmlspecialchars((string) ($jobPost['salary'] ?? '')) ?>"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label" for="contactEmail">Contact Email</label><input class="form-input" id="contactEmail" name="contactEmail" value="<?= htmlspecialchars($jobPost['contactEmail']) ?>"></div>
                        <div class="form-group"><label class="form-label" for="contactPhone">Contact Phone</label><input class="form-input" id="contactPhone" name="contactPhone" value="<?= htmlspecialchars($jobPost['contactPhone']) ?>"></div>
                    </div>
                    <div class="form-group"><label class="form-label" for="applicationUrl">Application URL</label><input class="form-input" id="applicationUrl" name="applicationUrl" value="<?= htmlspecialchars($jobPost['applicationUrl']) ?>"></div>
                    <div class="form-group"><label><input type="checkbox" name="isActive" value="1" <?= $jobPost['active'] ? 'checked' : '' ?>> Active Job Post</label></div>
                    <div class="form-actions"><a href="/admin/resources?tab=job-ads" class="admin-button admin-button--secondary">Cancel</a><button type="submit" class="admin-button admin-button--primary">Update Job Post</button></div>
                </form>
            <?php endif; ?>
        </div>
    </section>
</main>
</body>
</html>
