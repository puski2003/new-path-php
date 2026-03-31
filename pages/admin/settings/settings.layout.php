<?php
$pageTitle = 'Settings';
require_once __DIR__ . '/../common/admin.html.head.php';
?>
<main class="admin-main-container">
    <?php require_once __DIR__ . '/../common/admin.sidebar.php'; ?>
    <section class="admin-main-content">
        <h1>Settings</h1>
        <?php if ($message !== ''): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
        <div class="admin-sub-container-2">
            <h2>General Platform Settings</h2>
            <form method="POST" class="settings-form">
                <div class="admin-sub-container-1">
                    <div class="settings-form-group"><label>Platform Name</label><input type="text" name="platformName" value="<?= htmlspecialchars($settings['platformName']) ?>"></div>
                    <div class="settings-form-group"><label>Session Length (minutes)</label><input type="number" name="sessionLength" value="<?= $settings['sessionLength'] ?>"></div>
                </div>
                <div class="settings-form-row">
                    <div class="settings-form-group"><label><input type="checkbox" name="emailNotifications" value="1" <?= $settings['emailNotificationsEnabled'] ? 'checked' : '' ?>> Email Notifications</label></div>
                    <div class="settings-form-group"><label><input type="checkbox" name="smsNotifications" value="1" <?= $settings['smsNotificationsEnabled'] ? 'checked' : '' ?>> SMS Notifications</label></div>
                </div>
                <button type="submit" class="admin-button admin-button--primary">Save Changes</button>
            </form>
        </div>
        <div class="admin-sub-container-2">
            <div class="admin-sub-container-1" style="justify-content: space-between; align-items: center;"><h2>Admin Roles &amp; Permissions</h2><button type="button" class="admin-button admin-button--primary">Add New Role</button></div>
            <table class="admin-table"><thead class="admin-table-header"><tr class="admin-table-row"><th class="admin-table-th">Role Name</th><th class="admin-table-th">Permissions</th><th class="admin-table-th">Assigned Admins</th></tr></thead><tbody class="admin-table-body"><?php foreach ($roles as $index => $role): ?><tr class="admin-table-row <?= $index % 2 === 0 ? 'admin-table-row--even' : 'admin-table-row--odd' ?>"><td class="admin-table-td"><strong><?= htmlspecialchars($role['roleName']) ?></strong></td><td class="admin-table-td"><?= htmlspecialchars(implode(', ', $role['permissions'])) ?></td><td class="admin-table-td"><?= $role['assignedAdmins'] ?></td></tr><?php endforeach; ?></tbody></table>
        </div>
        <div class="admin-sub-container-2"><h2>Notification &amp; Policy Management</h2><div class="settings-subsection"><div class="notification-template-item"><div class="notification-template-info"><h4>Account Creation</h4><p>Welcome email sent to new users</p></div></div><div class="notification-template-item"><div class="notification-template-info"><h4>Password Reset</h4><p>Password reset instructions</p></div></div><div class="notification-template-item"><div class="notification-template-info"><h4>Counselor Approval</h4><p>Notification for counselor status updates</p></div></div></div></div>
    </section>
</main>
</body>
</html>
