<?php
$activePage = 'dashboard';
$welcomeName = (string) ($currentCounselor['displayName'] ?? 'Counselor');
?>
<!DOCTYPE html>
<html lang="en">
<?php
$pageTitle = 'Counselor Dashboard';
$pageStyle = ['counselor/dashboard'];
require_once __DIR__ . '/../common/counselor.html.head.php';
?>
<body>
<main class="main-container theme-counselor">
    <?php require_once __DIR__ . '/../common/counselor.sidebar.php'; ?>

    <section class="main-content">
        <img src="/assets/img/main-content-head.svg" alt="Main Content Head background" class="main-header-bg-image" />

        <div class="main-content-header">
            <div class="main-content-header-text">
                <h2>Welcome back, <?= htmlspecialchars($welcomeName) ?>! &#128075;</h2>
                <p>Here's an overview of the day.</p>
            </div>

            <div class="card-container">
                <div class="card days-sober-card" style="width: 200px;">
                    <div class="days-sober-content">
                        <p>INCOME</p>
                        <h1><?= htmlspecialchars((string) $totalIncome) ?>$</h1>
                    </div>
                    <p>This month</p>
                </div>

                <div class="card days-sober-card" style="width: 200px;">
                    <div class="days-sober-content">
                        <p>Active Clients</p>
                        <h1><?= htmlspecialchars((string) (!empty($activeClients) ? $activeClients : ($currentCounselor['totalClients'] ?? 0))) ?></h1>
                    </div>
                    <p>Total</p>
                </div>
            </div>
        </div>

        <div class="main-content-body">
            <div class="inner-body-content row">
                <div class="row">
                    <h3>Schedule</h3>
                    <?php require_once __DIR__ . '/../common/counselor.calendar.php'; ?>
                </div>

                <div class="row">
                    <h3>Upcoming Sessions</h3>
                    <?php if (!empty($upcomingSessions)): ?>
                        <?php foreach ($upcomingSessions as $session): ?>
                            <?php
                            $isUpcoming = true;
                            require __DIR__ . '/../common/counselor.session-card.php';
                            ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-sessions-message" style="text-align: center; padding: 20px; color: #9ca3af;">
                            No upcoming sessions scheduled.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <h3>Client Activity Feed</h3>
                    <?php if (!empty($clientActivities)): ?>
                        <?php foreach ($clientActivities as $activity): ?>
                            <div class="client-activity-card">
                                <div class="client-activity-card-info">
                                    <img src="<?= htmlspecialchars($activity['profilePicture']) ?>" alt="<?= htmlspecialchars($activity['clientName']) ?>">
                                    <div>
                                        <h4><?= htmlspecialchars($activity['clientName']) ?></h4>
                                        <span><?= htmlspecialchars($activity['description']) ?></span>
                                    </div>
                                </div>
                                <span><?= htmlspecialchars($activity['timeAgo']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="client-activity-card">
                            <div class="client-activity-card-info">
                                <img src="/assets/img/avatar.png" alt="Default avatar">
                                <div>
                                    <h4 style="color: #9ca3af;">No recent activity</h4>
                                    <span>Client activities will appear here</span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
    lucide.createIcons();
</script>
</body>
</html>
