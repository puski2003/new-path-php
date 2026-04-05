<?php
$sessionId      = (int)($session['sessionId'] ?? 0);
$doctorName     = (string)($session['doctorName'] ?? 'Dr. Amelia Harper');
$specialty      = (string)($session['specialty'] ?? 'Addiction Specialist');
$profilePicture = (string)($session['profilePicture'] ?? '/assets/img/avatar.png');
$schedule       = (string)($session['schedule'] ?? '');
$sessionType    = (string)($session['sessionType'] ?? 'upcoming');
$meetingLink    = (string)($session['meetingLink'] ?? '');
?>
<div class="session-card" data-session-id="<?= $sessionId ?>">
    <div class="session-avatar">
        <img src="<?= htmlspecialchars($profilePicture) ?>" alt="<?= htmlspecialchars($doctorName) ?>" />
    </div>
    <div class="session-info">
        <span class="session-specialty"><?= htmlspecialchars($specialty) ?></span>
        <h3 class="session-name"><?= htmlspecialchars($doctorName) ?></h3>
        <p class="session-schedule"><?= htmlspecialchars($schedule) ?></p>
        <a href="/user/sessions?id=<?= $sessionId ?>" class="btn btn-bg-light-green btn-view-more">View More</a>
    </div>
    <div class="session-actions">
        <?php if ($sessionType === 'upcoming'): ?>
            <?php if (!empty($meetingLink)): ?>
                <a class="btn btn-primary" href="<?= htmlspecialchars($meetingLink) ?>" target="_blank" rel="noopener">Join Now</a>
            <?php else: ?>
                <button class="btn btn-primary" type="button" disabled>Join Now</button>
            <?php endif; ?>
        <?php else: ?>
            <?php $hasReview = !empty($session['hasReview']); ?>
            <?php if ($hasReview): ?>
                <button class="btn btn-secondary" type="button" disabled>Reviewed ✓</button>
            <?php else: ?>
                <a class="btn btn-secondary" href="/user/sessions?id=<?= $sessionId ?>&review=1">Leave Review</a>
            <?php endif; ?>
            <a class="btn btn-primary" href="/user/counselors?id=<?= (int)($session['counselorId'] ?? 0) ?>">Rebook</a>
        <?php endif; ?>
    </div>
</div>
