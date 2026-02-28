<?php
$sessionId = (int)($session['sessionId'] ?? 0);
$doctorName = (string)($session['doctorName'] ?? 'Dr. Amelia Harper');
$specialty = (string)($session['specialty'] ?? 'Addiction Specialist');
$schedule = (string)($session['schedule'] ?? '');
$sessionType = (string)($session['sessionType'] ?? 'upcoming');
?>
<div class="session-card" data-session-id="<?= $sessionId ?>">
    <div class="session-avatar">
        <img src="/assets/img/avatar.png" alt="<?= htmlspecialchars($doctorName) ?>" />
    </div>
    <div class="session-info">
        <span class="session-specialty"><?= htmlspecialchars($specialty) ?></span>
        <h3 class="session-name"><?= htmlspecialchars($doctorName) ?></h3>
        <p class="session-schedule"><?= htmlspecialchars($schedule) ?></p>
        <a href="/user/sessions?id=<?= $sessionId ?>" class="btn btn-bg-light-green btn-view-more">View More</a>
    </div>
    <div class="session-actions">
        <?php if ($sessionType === 'upcoming'): ?>
            <button class="btn btn-primary" type="button">Join Now</button>
        <?php else: ?>
            <button class="btn btn-secondary" type="button">Review</button>
            <button class="btn btn-primary" type="button">Rebook</button>
        <?php endif; ?>
    </div>
</div>
