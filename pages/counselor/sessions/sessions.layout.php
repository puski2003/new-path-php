<?php
$activePage = 'sessions';
$pageScripts = [
    '/assets/js/counselor/sessions/sessions.js',
    '/assets/js/counselor/sessions/rescheduleRequests.js',
];
?>
<!DOCTYPE html>
<html lang="en">
<?php $pageTitle = 'Schedule'; $pageStyle = ['counselor/sessions']; require __DIR__ . '/../common/counselor.html.head.php'; ?>
<body>
<main class="main-container theme-counselor">
    <?php require __DIR__ . '/../common/counselor.sidebar.php'; ?>

    <section class="main-content">
        <?php require __DIR__ . '/../common/counselor.page-header.php'; ?>

        <div class="main-content-body ">
            <div class="inner-body-content">
                <div class="body-column">

                    <?php require __DIR__ . '/../common/counselor.toolbar.php'; ?>

                    <div class="dashboard-card counselor-tab-card">
                        <div class="counselor-tab-row">
                            <span onclick="showSection('tab-today')"     class="toggle-button active-button" id="btn-today">Today</span>
                            <span onclick="showSection('tab-upcoming')"  class="toggle-button"               id="btn-upcoming">Upcoming</span>
                            <span onclick="showSection('tab-completed')" class="toggle-button"               id="btn-completed">Completed</span>
                            <span onclick="showSection('tab-cancelled')" class="toggle-button"               id="btn-cancelled">Cancelled / No-show</span>
                            <span onclick="showSection('tab-reschedule')" class="toggle-button"              id="btn-reschedule">Reschedule Requests <span class="reschedule-badge" id="rescheduleBadge" style="display:none;"></span></span>
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

                        <!-- Reschedule Requests (loaded via AJAX) -->
                        <section class="toggle-section" id="tab-reschedule">
                            <div id="reschedule-requests-list">
                                <p class="empty-state-text">Loading requests…</p>
                            </div>
                        </section>

                    </div><!-- /.counselor-list-card -->

                </div><!-- /.body-column -->
            </div><!-- /.inner-body-content -->
        </div><!-- /.main-content-body -->
    </section>
    <!-- Reschedule request review modal -->
    <div class="session-modal-overlay" id="rescheduleReviewOverlay" style="display:none;">
        <div class="session-modal">
            <div class="session-modal-header">
                <h3 id="rescheduleReviewTitle">Review Request</h3>
                <button type="button" class="session-modal-close" id="closeRescheduleReview">&times;</button>
            </div>
            <div class="session-modal-body">
                <p id="rescheduleReviewMeta" style="color:var(--color-text-secondary);margin-bottom:var(--spacing-md);font-size:var(--font-size-sm);"></p>
                <p id="rescheduleReviewReason" style="margin-bottom:var(--spacing-lg);"></p>
                <div class="form-group">
                    <label for="rescheduleReviewNote">Note to client <span class="optional">(optional)</span></label>
                    <textarea class="form-input" id="rescheduleReviewNote" rows="3" maxlength="500"
                        placeholder="Briefly explain your decision…"></textarea>
                </div>
                <p id="rescheduleReviewError" style="color:#dc2626;font-size:var(--font-size-sm);display:none;"></p>
                <div class="session-modal-actions">
                    <button type="button" class="btn btn-secondary" id="closeRescheduleReview2">Cancel</button>
                    <button type="button" class="btn btn-danger"    id="rejectRescheduleBtn">Decline</button>
                    <button type="button" class="btn btn-primary"   id="approveRescheduleBtn">Approve</button>
                </div>
            </div>
        </div>
    </div>
</main>


<?php require __DIR__ . '/../common/counselor.footer.php'; ?>
</body>
</html>
