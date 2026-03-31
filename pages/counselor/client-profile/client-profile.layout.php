<?php $activePage = 'clients'; ?>
<!DOCTYPE html>
<html lang="en">
<?php $pageTitle = 'Counselor Client Profile'; $pageStyle = ['counselor/clientProfile']; require __DIR__ . '/../common/counselor.html.head.php'; ?>
<body>
<main class="main-container theme-counselor">
    <?php require __DIR__ . '/../common/counselor.sidebar.php'; ?>
    <section class="main-content">
        <img src="/assets/img/main-content-head.svg" alt="Main Content Head background" class="main-header-bg-image" />
        <div class="main-content-header">
            <div class="main-content-header-text">
                <h2><?= htmlspecialchars($clientProfile['name']) ?></h2>
                <p>View <?= htmlspecialchars($clientProfile['name']) ?>'s sessions and recovery plan</p>
            </div>
            <div class="card-container">
                <div class="card milestone-progress-card">
                    <p>PLAN PROGRESS</p>
                    <span><?= (int) $clientProfile['progressPercentage'] ?>%</span>
                    <div class="progress" style="--value: <?= (int) $clientProfile['progressPercentage'] ?>%">
                        <div class="bar"></div>
                        <div class="thumb" aria-label="Progress <?= (int) $clientProfile['progressPercentage'] ?> percent"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="main-content-body">
            <div class="inner-body-content row">
                <!-- Client basic info (demographics only per PRD §3.3 / §9.1) -->
                <div class="client-card">
                    <div class="client-card-content">
                        <div class="client-card-info">
                            <h4><?= htmlspecialchars($clientProfile['name']) ?></h4>
                            <span><?= htmlspecialchars($clientProfile['status']) ?></span>
                        </div>
                    </div>
                    <img src="<?= htmlspecialchars($clientProfile['avatarUrl']) ?>" alt="Client" />
                </div>

                <!-- Session summary (this counselor's sessions only) -->
                <h3>Sessions with You</h3>
                <div class="client-card">
                    <div class="client-card-content">
                        <div class="client-card-info">
                            <h4><?= (int) $clientProfile['totalSessions'] ?> session<?= $clientProfile['totalSessions'] !== 1 ? 's' : '' ?> booked</h4>
                            <span><?= (int) $clientProfile['completedSessions'] ?> completed</span>
                        </div>
                    </div>
                </div>

                <!-- Recovery plan (assigned by this counselor only) -->
                <h3>Recovery Plan</h3>
                <div class="client-card">
                    <div class="client-card-content">
                        <div class="client-card-info">
                            <h4><?= htmlspecialchars($clientProfile['plan']['title'] ?? 'No plan assigned yet') ?></h4>
                            <span><?= htmlspecialchars(ucfirst($clientProfile['plan']['status'] ?? '')) ?></span>
                        </div>
                        <div class="client-card-buttons">
                            <?php if (!empty($clientProfile['plan']['planId'])): ?>
                                <a href="/counselor/recovery-plans/view?planId=<?= (int) $clientProfile['plan']['planId'] ?>">
                                    <button class="btn-join" type="button">View Full Plan</button>
                                </a>
                            <?php else: ?>
                                <a href="/counselor/recovery-plans/create?userId=<?= (int) $clientProfile['id'] ?>">
                                    <button class="btn-join" type="button">Create Plan</button>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <img src="/assets/img/plan.png" alt="Plan" />
                </div>

                <!-- Session notes (counselor-only, private) -->
                <div class="row session-notes">
                    <h3>Session Notes</h3>
                    <span><?= htmlspecialchars($clientProfile['sessionNotes']) ?></span>
                    <button class="btn-join" type="button">Add Notes</button>
                </div>
            </div>
        </div>
    </section>
</main>
<script src="/assets/js/counselor/clientProfile.js"></script>
<script>lucide.createIcons();</script>
</body>
</html>
