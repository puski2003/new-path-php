<?php
$activePage = 'sessions';
?>
<!DOCTYPE html>
<html lang="en">
<?php $pageTitle = 'Schedule'; $pageStyle = ['counselor/sessions']; require __DIR__ . '/../common/counselor.html.head.php'; ?>
<body>
<main class="main-container theme-counselor">
    <?php require __DIR__ . '/../common/counselor.sidebar.php'; ?>

    <section class="main-content">
        <?php require __DIR__ . '/../common/counselor.page-header.php'; ?>

        <div class="main-content-body dashboard-overflow">
            <div class="inner-body-content">
                <div class="body-column">

                    <?php require __DIR__ . '/../common/counselor.toolbar.php'; ?>

                    <div class="dashboard-card counselor-tab-card">
                        <div class="counselor-tab-row">
                            <span onclick="showSection('tab-today')"     class="toggle-button active-button" id="btn-today">Today</span>
                            <span onclick="showSection('tab-upcoming')"  class="toggle-button"               id="btn-upcoming">Upcoming</span>
                            <span onclick="showSection('tab-completed')" class="toggle-button"               id="btn-completed">Completed</span>
                            <span onclick="showSection('tab-cancelled')" class="toggle-button"               id="btn-cancelled">Cancelled / No-show</span>
                        </div>
                    </div>

                    <?php $cardTitle = null; $cardAction = null; $cardClass = 'counselor-list-card';
                    require __DIR__ . '/../common/counselor.section-card.php'; ?>

                        <!-- Today -->
                        <section class="toggle-section active-section" id="tab-today">
                            <?php if (!empty($tabToday)): ?>
                                <?php foreach ($tabToday as $session): $isUpcoming = true; require __DIR__ . '/../common/counselor.session-card.php'; endforeach; ?>
                            <?php else: ?>
                                <?php $emptyStateMessage = 'No sessions scheduled for today.'; require __DIR__ . '/../common/counselor.empty-state.php'; ?>
                            <?php endif; ?>
                        </section>

                        <!-- Upcoming -->
                        <section class="toggle-section" id="tab-upcoming">
                            <?php if (!empty($tabUpcoming)): ?>
                                <?php foreach ($tabUpcoming as $session): $isUpcoming = true; require __DIR__ . '/../common/counselor.session-card.php'; endforeach; ?>
                            <?php else: ?>
                                <?php $emptyStateMessage = 'No upcoming sessions.'; require __DIR__ . '/../common/counselor.empty-state.php'; ?>
                            <?php endif; ?>
                        </section>

                        <!-- Completed -->
                        <section class="toggle-section" id="tab-completed">
                            <?php if (!empty($tabCompleted)): ?>
                                <?php foreach ($tabCompleted as $session): $isUpcoming = false; require __DIR__ . '/../common/counselor.session-card.php'; endforeach; ?>
                            <?php else: ?>
                                <?php $emptyStateMessage = 'No completed sessions yet.'; require __DIR__ . '/../common/counselor.empty-state.php'; ?>
                            <?php endif; ?>
                        </section>

                        <!-- Cancelled / No-show -->
                        <section class="toggle-section" id="tab-cancelled">
                            <?php if (!empty($tabCancelled)): ?>
                                <?php foreach ($tabCancelled as $session): $isUpcoming = false; require __DIR__ . '/../common/counselor.session-card.php'; endforeach; ?>
                            <?php else: ?>
                                <?php $emptyStateMessage = 'No cancelled or missed sessions.'; require __DIR__ . '/../common/counselor.empty-state.php'; ?>
                            <?php endif; ?>
                        </section>

                    </div><!-- /.counselor-list-card -->

                </div><!-- /.body-column -->
            </div><!-- /.inner-body-content -->
        </div><!-- /.main-content-body -->
    </section>
</main>

<?php require __DIR__ . '/../common/counselor.followup-popup.php'; ?>

<script>
function showSection(id) {
    document.querySelectorAll('.toggle-section').forEach(s => s.classList.remove('active-section'));
    document.querySelectorAll('.toggle-button').forEach(b => b.classList.remove('active-button'));
    const section = document.getElementById(id);
    if (section) section.classList.add('active-section');
    const btnMap = { 'tab-today': 'btn-today', 'tab-upcoming': 'btn-upcoming', 'tab-completed': 'btn-completed', 'tab-cancelled': 'btn-cancelled' };
    const btn = document.getElementById(btnMap[id]);
    if (btn) btn.classList.add('active-button');
}
</script>
<script src="/assets/js/counselor/followUp.js"></script>
<script>lucide.createIcons();</script>
</body>
</html>
