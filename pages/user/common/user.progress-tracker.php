<div class="recovery-section progress-tracker">
    <div class="section-header">
        <h3 class="section-title">Progress Tracker</h3>
        <a href="/user/recovery/progress" class="btn btn-link view-all-progress-btn">View All Progress</a>
    </div>

    <div class="current-streak">
        <div class="streak-info">
            <span class="streak-number"><?= $daysSober ?></span>
            <span class="streak-label">Days Sober</span>
        </div>
        <div class="streak-visual">
            <div class="streak-ring">
                <svg viewBox="0 0 100 100" class="streak-svg">
                    <circle cx="50" cy="50" r="45" fill="none" stroke="var(--color-progress-bg)" stroke-width="8" />
                    <circle cx="50" cy="50" r="45" fill="none" stroke="var(--color-primary)" stroke-width="8" stroke-dasharray="282.7" stroke-dashoffset="<?= htmlspecialchars($strokeOffset) ?>" transform="rotate(-90 50 50)" />
                </svg>
                <div class="streak-percentage"><?= $progressCirclePercentage ?>%</div>
            </div>
        </div>
    </div>

    <div class="progress-stats">
        <div class="stat-item">
            <span class="stat-value"><?= $totalDaysTracked ?></span>
            <span class="stat-label">Total Days Tracked</span>
        </div>
        <div class="stat-item">
            <span class="stat-value"><?= $urgesLogged ?></span>
            <span class="stat-label">Urges Logged</span>
        </div>
        <div class="stat-item">
            <span class="stat-value"><?= $sessionsCompleted ?></span>
            <span class="stat-label">Sessions Completed</span>
        </div>
    </div>

    <?php require __DIR__ . '/user.achievements-mini.php'; ?>

    <div class="recent-logs">
        <h4 class="logs-title">Recent Activity</h4>
        <div class="logs-list">
            <?php if ($daysSober > 0): ?>
                <div class="log-item">
                    <div class="log-icon mood-positive">??</div>
                    <div class="log-details">
                        <span class="log-text"><?= $daysSober ?> days sober streak!</span>
                        <span class="log-time">Current streak</span>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($sessionsCompleted > 0): ?>
                <div class="log-item">
                    <div class="log-icon session-completed">?</div>
                    <div class="log-details">
                        <span class="log-text"><?= $sessionsCompleted ?> sessions completed</span>
                        <span class="log-time">Total sessions</span>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($urgesLogged > 0): ?>
                <div class="log-item">
                    <div class="log-icon urge-managed">?</div>
                    <div class="log-details">
                        <span class="log-text"><?= $urgesLogged ?> urges managed</span>
                        <span class="log-time">Total logged</span>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($daysSober === 0 && $sessionsCompleted === 0 && $urgesLogged === 0): ?>
                <div class="log-item">
                    <div class="log-icon">??</div>
                    <div class="log-details">
                        <span class="log-text">Start tracking your progress</span>
                        <span class="log-time">No activity yet</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="progress-actions">
        <?php if ($daysSober === 0): ?>
            <form action="/user/recovery/start-sobriety" method="post">
                <button type="submit" class="btn btn-primary start-tracking-btn">Start Tracking</button>
            </form>
        <?php else: ?>
            <button type="button" class="btn btn-primary log-progress-btn">Log Progress</button>
            <button type="button" class="btn btn-secondary reset-btn" onclick="showResetModal()">Reset Counter</button>
        <?php endif; ?>
        <a href="/user/recovery/progress" class="btn btn-link view-analytics-btn">View Analytics</a>
    </div>

    <div id="resetSobrietyModal" class="modal-overlay" style="display: none">
        <div class="modal-content reset-modal">
            <h4>Reset Sobriety Counter?</h4>
            <p>This will reset your counter to Day 0. Your earned achievements will be preserved.</p>
            <form action="/user/recovery/reset-sobriety" method="post">
                <div class="form-group">
                    <label for="resetReason">Reason (optional):</label>
                    <textarea name="reason" id="resetReason" rows="2" placeholder="What happened?"></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="hideResetModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reset Counter</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showResetModal() {
            document.getElementById('resetSobrietyModal').style.display = 'flex';
        }
        function hideResetModal() {
            document.getElementById('resetSobrietyModal').style.display = 'none';
        }
    </script>
</div>
