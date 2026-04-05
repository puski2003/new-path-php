<?php
$pageTitle = 'Add Job Post';
require_once __DIR__ . '/../../common/admin.html.head.php';
?>
<main class="admin-main-container">
    <?php require_once __DIR__ . '/../../common/admin.sidebar.php'; ?>
    <section class="admin-main-content">
        <div class="admin-sub-container-1" style="justify-content: space-between; align-items: center;">
            <h1>Add Job Post</h1>
            <a href="/admin/resources?tab=job-ads" class="admin-button admin-button--secondary"><span class="button-text">Back to Job Ads</span></a>
        </div>
        <div class="admin-sub-container-2">
            <?php if ($error !== ''): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <?php if ($success !== ''): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
            <form method="POST" class="admin-form" style="max-width: 800px;">
                <?php foreach ([
                    'title' => 'Job Title *',
                    'company' => 'Company Name *',
                    'location' => 'Location *',
                    'salaryRange' => 'Salary Range',
                    'contactEmail' => 'Contact Email',
                    'contactPhone' => 'Contact Phone',
                    'applicationUrl' => 'Application URL',
                ] as $field => $label): ?>
                    <div class="form-group"><label class="form-label" for="<?= $field ?>"><?= $label ?></label><input class="form-input" type="<?= str_contains($field, 'Email') ? 'email' : (str_contains($field, 'Url') ? 'url' : 'text') ?>" id="<?= $field ?>" name="<?= $field ?>" value="<?= htmlspecialchars($_POST[$field] ?? '') ?>"></div>
                <?php endforeach; ?>
                <div class="form-row">
                    <div class="form-group"><label class="form-label" for="category">Category *</label><input class="form-input" id="category" name="category" value="<?= htmlspecialchars($_POST['category'] ?? '') ?>"></div>
                    <div class="form-group"><label class="form-label" for="jobType">Job Type *</label><select class="form-select" id="jobType" name="jobType"><?php foreach (['Full-time','Part-time','Contract','Temporary','Internship'] as $jobType): ?><option value="<?= $jobType ?>" <?= ($_POST['jobType'] ?? '') === $jobType ? 'selected' : '' ?>><?= $jobType ?></option><?php endforeach; ?></select></div>
                </div>
                <div class="form-group"><label class="form-label" for="description">Job Description *</label><textarea class="form-textarea" id="description" name="description" rows="6"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea></div>
                <div class="form-group"><label class="form-label" for="requirements">Requirements</label><textarea class="form-textarea" id="requirements" name="requirements" rows="4"><?= htmlspecialchars($_POST['requirements'] ?? '') ?></textarea></div>
                <div class="form-actions"><a href="/admin/resources?tab=job-ads" class="admin-button admin-button--secondary">Cancel</a><button type="submit" class="admin-button admin-button--primary">Create Job Post</button></div>
            </form>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../../common/admin.footer.php'; ?>
</body>
</html>
