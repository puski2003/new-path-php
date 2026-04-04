<?php
$session    = $session ?? [];
$isUpcoming = !empty($isUpcoming);

$sessionTs  = !empty($session['sessionDatetime']) ? strtotime((string) $session['sessionDatetime']) : null;
$displayTime = $sessionTs ? date('D, M j \a\t g:i A', $sessionTs) : 'Schedule unavailable';
$clientName  = $session['userName'] ?? 'Client';
$sessionType = $session['sessionType'] ?? 'video';
$status      = $session['status'] ?? ($isUpcoming ? 'scheduled' : 'completed');

$typeLabel = match ($sessionType) {
    'in_person' => 'In Person',
    'audio'     => 'Audio',
    'chat'      => 'Chat',
    default     => 'Video',
};
?>
<div class="counselor-session-card" data-session-id="<?= (int) ($session['sessionId'] ?? 0) ?>">
    <div class="counselor-session-info">
        <h4><?= htmlspecialchars($clientName) ?></h4>
        <span><?= htmlspecialchars($displayTime) ?></span>
        <div class="session-card-meta">
            <span class="session-type-pill"><?= htmlspecialchars($typeLabel) ?></span>
            <span class="plan-status status-<?= htmlspecialchars($status) ?>"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $status))) ?></span>
        </div>
        <?php if ($isUpcoming): ?>
            <div class="session-action-row">
                <?php if (!empty($session['meetingLink'])): ?>
                    <a class="btn-join" href="<?= htmlspecialchars($session['meetingLink']) ?>" target="_blank" rel="noopener">Join</a>
                <?php else: ?>
                    <button class="btn-join" type="button" disabled>No Link</button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="session-action-row">
                <button class="btn-join" type="button">View Notes</button>
                <button class="btn-warning" type="button">Report</button>
            </div>
        <?php endif; ?>
    </div>
    <img src="/assets/img/avatar.png" alt="Client avatar" class="counselors-image" />
</div>
