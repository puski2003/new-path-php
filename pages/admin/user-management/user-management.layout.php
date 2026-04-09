<?php
$pageTitle = 'User Management';
require_once __DIR__ . '/../common/admin.html.head.php';
?>
<main class="admin-main-container">
    <?php require_once __DIR__ . '/../common/admin.sidebar.php'; ?>

    <section class="admin-main-content">
        <h1>User Management</h1>

        <form method="GET" action="/admin/user-management" class="admin-sub-container-2" style="padding: var(--spacing-lg); border-radius: var(--radius-sm);">
            <div class="admin-sub-container-1" style="justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <div class="admin-sub-container-1" style="flex-wrap: wrap;">
                    <label>Role:
                        <select name="role" class="admin-dropdown">
                            <?php foreach (['all' => 'All Roles', 'Recovering User' => 'Recovering User', 'Counselor' => 'Counselor', 'Admin' => 'Admin'] as $value => $label): ?>
                                <option value="<?= htmlspecialchars($value) ?>" <?= $filters['role'] === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>Status:
                        <select name="status" class="admin-dropdown">
                            <?php foreach (['all' => 'All Status', 'Active' => 'Active', 'Pending' => 'Pending', 'Inactive' => 'Inactive'] as $value => $label): ?>
                                <option value="<?= htmlspecialchars($value) ?>" <?= $filters['status'] === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>Date Joined:
                        <input type="date" name="dateJoined" value="<?= htmlspecialchars($filters['dateJoined']) ?>">
                    </label>
                    <label>Engagement:
                        <select name="engagement" class="admin-dropdown">
                            <?php foreach (['all' => 'All Levels', 'High' => 'High', 'Medium' => 'Medium', 'Low' => 'Low'] as $value => $label): ?>
                                <option value="<?= htmlspecialchars($value) ?>" <?= $filters['engagement'] === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>
                <button type="submit" class="admin-button admin-button--primary"><span class="button-text">Apply Filters</span></button>
            </div>

            <div class="admin-sub-container-1" style="justify-content: space-between; align-items: center;">
                <input type="text" name="search" class="admin-searchbar" placeholder="Search by name or email..." value="<?= htmlspecialchars($filters['search']) ?>" style="max-width:400px;">
                <a href="/admin/user-management" class="admin-button admin-button--secondary"><span class="button-text">Reset</span></a>
            </div>
        </form>

        <div class="admin-sub-container-2">
            <table class="admin-table">
                <thead class="admin-table-header">
                <tr class="admin-table-row">
                    <th class="admin-table-th">User ID</th>
                    <th class="admin-table-th">Full Name</th>
                    <th class="admin-table-th">Role</th>
                    <th class="admin-table-th">Status</th>
                    <th class="admin-table-th">Last Active</th>
                    <th class="admin-table-th">Registration</th>
                    <th class="admin-table-th">Actions</th>
                </tr>
                </thead>
                <tbody class="admin-table-body">
                <?php if ($users === []): ?>
                    <tr class="admin-table-row">
                        <td class="admin-table-td" colspan="7">No users found.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($users as $index => $item): ?>
                    <tr class="admin-table-row <?= $index % 2 === 0 ? 'admin-table-row--even' : 'admin-table-row--odd' ?>">
                        <td class="admin-table-td">#<?= $item['userId'] ?></td>
                        <td class="admin-table-td"><strong><?= htmlspecialchars($item['fullName']) ?></strong><br><small><?= htmlspecialchars($item['email']) ?></small></td>
                        <td class="admin-table-td"><?= htmlspecialchars($item['role']) ?></td>
                        <td class="admin-table-td"><?= htmlspecialchars($item['status']) ?></td>
                        <td class="admin-table-td"><?= htmlspecialchars($item['lastActive']) ?></td>
                        <td class="admin-table-td"><?= htmlspecialchars($item['registration']) ?></td>
                        <td class="admin-table-td admin-table-td--action">
                            <div class="admin-table-actions">
                                <a href="/admin/user-management/edit?id=<?= $item['userId'] ?>" class="admin-button admin-button--ghost">Edit</a>
                                <button
                                    type="button"
                                    class="admin-button admin-button--danger"
                                    onclick="deleteUser(<?= $item['userId'] ?>, <?= json_encode($item['fullName']) ?>)"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<script>
(function () {
    const params = new URLSearchParams(window.location.search);
    const type = params.get('alertType');
    const message = params.get('alertMessage');

    if (message && window.NewPathAlert) {
        if (type === 'success') NewPathAlert.success(message);
        else if (type === 'warning') NewPathAlert.warning(message);
        else if (type === 'error') NewPathAlert.error(message);
        else NewPathAlert.info(message);
    }
})();

function deleteUser(userId, fullName) {
    if (!confirm('Delete "' + fullName + '"? This action cannot be undone.')) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/user-management/delete';

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'userId';
    input.value = String(userId);

    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
}
</script>

<?php require_once __DIR__ . '/../common/admin.footer.php'; ?>
</body>
</html>
