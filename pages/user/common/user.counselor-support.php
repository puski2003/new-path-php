<div class="recovery-section counselor-support">
    <h3 class="section-title">Counselor Support</h3>

    <div class="next-session">
        <div class="session-info">
            <span class="session-label">Next Session</span>
            <span class="session-time"><?= htmlspecialchars($nextSessionTime) ?> with <?= htmlspecialchars($counselorName) ?></span>
        </div>
        <a href="/user/sessions" class="btn btn-primary join-session-btn">Join Session</a>
    </div>

    <div class="counselor-notes">
        <h4 class="notes-title">Notes from Counselor</h4>
        <p class="notes-content"><?= htmlspecialchars($counselorNotes) ?></p>
        <a href="/user/community" class="btn btn-bg-light-green request-feedback-btn">Request Feedback</a>
    </div>
</div>
