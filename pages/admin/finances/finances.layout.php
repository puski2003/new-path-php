<?php
$pageTitle = 'Finances';
require_once __DIR__ . '/../common/admin.html.head.php';
?>
<main class="admin-main-container">
    <?php require_once __DIR__ . '/../common/admin.sidebar.php'; ?>
    <section class="admin-main-content">
        <h1>Finances</h1>
        <div class="admin-sub-container-1">
            <div class="admin-summary-card"><div class="admin-summary-card-content"><p class="admin-summary-card-title">Total Revenue</p><p class="admin-summary-card-info">$<?= number_format($summary['totalRevenue'], 2) ?></p></div></div>
            <div class="admin-summary-card"><div class="admin-summary-card-content"><p class="admin-summary-card-title">Sessions Paid</p><p class="admin-summary-card-info"><?= $summary['sessionsPaid'] ?></p></div></div>
            <div class="admin-summary-card"><div class="admin-summary-card-content"><p class="admin-summary-card-title">Avg Payment</p><p class="admin-summary-card-info">$<?= number_format($summary['avgPayment'], 2) ?></p></div></div>
            <div class="admin-summary-card"><div class="admin-summary-card-content"><p class="admin-summary-card-title">Pending Refunds</p><p class="admin-summary-card-info"><?= $summary['pendingRefunds'] ?></p></div></div>
        </div>
        <div class="admin-sub-container-2"><div class="admin-data-card" style="height: 260px;"><h3>Monthly Revenue Trend</h3><p class="data-card__placeholder">Chart placeholder</p></div></div>
        <div class="admin-sub-container-2">
            <form method="GET" class="admin-sub-container-1" style="justify-content: space-between;">
                <h2>Refunds and Disputes</h2>
                <div class="admin-sub-container-1"><select name="disputeStatus" class="admin-dropdown"><option value="all">All Status</option><option value="pending" <?= $filters['disputeStatus'] === 'pending' ? 'selected' : '' ?>>Pending</option><option value="resolved" <?= $filters['disputeStatus'] === 'resolved' ? 'selected' : '' ?>>Resolved</option><option value="rejected" <?= $filters['disputeStatus'] === 'rejected' ? 'selected' : '' ?>>Rejected</option></select><input type="text" name="disputeIssue" value="<?= htmlspecialchars($filters['disputeIssue'] === 'allIssues' ? '' : $filters['disputeIssue']) ?>" placeholder="Issue type"><button class="admin-button admin-button--secondary">Refresh</button></div>
            </form>
            <table class="admin-table"><thead class="admin-table-header"><tr class="admin-table-row"><th class="admin-table-th">Transaction ID</th><th class="admin-table-th">User</th><th class="admin-table-th">Counselor</th><th class="admin-table-th">Amount</th><th class="admin-table-th">Issue</th><th class="admin-table-th">Status</th></tr></thead><tbody class="admin-table-body"><?php foreach ($disputes as $index => $dispute): ?><tr class="admin-table-row <?= $index % 2 === 0 ? 'admin-table-row--even' : 'admin-table-row--odd' ?>"><td class="admin-table-td"><?= htmlspecialchars($dispute['transactionId']) ?></td><td class="admin-table-td"><?= htmlspecialchars($dispute['userName']) ?></td><td class="admin-table-td"><?= htmlspecialchars($dispute['counselorName']) ?></td><td class="admin-table-td">$<?= htmlspecialchars($dispute['amount']) ?></td><td class="admin-table-td"><?= htmlspecialchars($dispute['issue']) ?></td><td class="admin-table-td"><?= htmlspecialchars(ucfirst($dispute['status'])) ?></td></tr><?php endforeach; ?></tbody></table>
        </div>
        <div class="admin-sub-container-2">
            <div class="admin-sub-container-1" style="justify-content: space-between; align-items: center;"><h2>Transaction Logs</h2><form method="GET" class="admin-sub-container-1"><input type="text" name="search" placeholder="Search transactions" value="<?= htmlspecialchars($search) ?>"><button class="admin-button admin-button--secondary">Search</button></form></div>
            <table class="admin-table"><thead class="admin-table-header"><tr class="admin-table-row"><th class="admin-table-th">Transaction ID</th><th class="admin-table-th">User</th><th class="admin-table-th">Counselor</th><th class="admin-table-th">Date</th><th class="admin-table-th">Amount</th><th class="admin-table-th">Payment Method</th><th class="admin-table-th">Status</th></tr></thead><tbody class="admin-table-body"><?php foreach ($transactions as $index => $transaction): ?><tr class="admin-table-row <?= $index % 2 === 0 ? 'admin-table-row--even' : 'admin-table-row--odd' ?>"><td class="admin-table-td"><?= htmlspecialchars($transaction['transactionId']) ?></td><td class="admin-table-td"><?= htmlspecialchars($transaction['userName']) ?></td><td class="admin-table-td"><?= htmlspecialchars($transaction['counselorName']) ?></td><td class="admin-table-td"><?= htmlspecialchars($transaction['date']) ?></td><td class="admin-table-td">$<?= htmlspecialchars($transaction['amount']) ?></td><td class="admin-table-td"><?= htmlspecialchars($transaction['paymentMethod']) ?></td><td class="admin-table-td"><?= htmlspecialchars(ucfirst($transaction['status'])) ?></td></tr><?php endforeach; ?></tbody></table>
        </div>
    </section>
</main>
</body>
</html>
