<?php
$pageTitle = 'Add Help Center';
require_once __DIR__ . '/../../common/admin.html.head.php';
?>
<main class="admin-main-container">
    <?php require_once __DIR__ . '/../../common/admin.sidebar.php'; ?>
    <section class="admin-main-content">
        <div class="admin-sub-container-1" style="justify-content: space-between; align-items: center;">
            <h1>Add Help Center</h1>
            <a href="/admin/resources?tab=help-centers" class="admin-button admin-button--secondary"><span class="button-text">Back to Help Centers</span></a>
        </div>
        <div class="admin-sub-container-2">
            <?php if ($error !== ''): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <?php if ($success !== ''): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
            <form method="POST" class="admin-form" style="max-width: 800px;">
                <?php foreach (['name' => 'Help Center Name *', 'organization' => 'Organization', 'phoneNumber' => 'Phone Number', 'email' => 'Email', 'website' => 'Website', 'address' => 'Address', 'city' => 'City', 'state' => 'State', 'zipCode' => 'ZIP Code', 'availability' => 'Availability'] as $field => $label): ?>
                    <div class="form-group"><label class="form-label" for="<?= $field ?>"><?= $label ?></label><input class="form-input" id="<?= $field ?>" name="<?= $field ?>" value="<?= htmlspecialchars($_POST[$field] ?? '') ?>"></div>
                <?php endforeach; ?>
                <div class="form-row">
                    <div class="form-group"><label class="form-label" for="type">Type *</label><input class="form-input" id="type" name="type" value="<?= htmlspecialchars($_POST['type'] ?? '') ?>"></div>
                    <div class="form-group"><label class="form-label" for="category">Category *</label><input class="form-input" id="category" name="category" value="<?= htmlspecialchars($_POST['category'] ?? '') ?>"></div>
                </div>
                <div class="form-group"><label class="form-label" for="description">Description *</label><textarea class="form-textarea" id="description" name="description" rows="5"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea></div>
                <div class="form-group"><label class="form-label" for="specialties">Specialties</label><textarea class="form-textarea" id="specialties" name="specialties" rows="3"><?= htmlspecialchars($_POST['specialties'] ?? '') ?></textarea></div>
                <div class="form-group"><label><input type="checkbox" name="isActive" value="1" <?= !isset($_POST['isActive']) || !empty($_POST['isActive']) ? 'checked' : '' ?>> Active (visible to users)</label></div>
                <div class="form-actions"><a href="/admin/resources?tab=help-centers" class="admin-button admin-button--secondary">Cancel</a><button type="submit" class="admin-button admin-button--primary">Create Help Center</button></div>
            </form>
        </div>
    </section>
</main>
</body>
</html>
