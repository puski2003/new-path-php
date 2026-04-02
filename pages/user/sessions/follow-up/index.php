<?php
/**
 * /user/sessions/follow-up — Post-session follow-up thread
 * GET  ?session_id=X  → view thread
 * POST               → send a message
 */
require_once __DIR__ . '/../../common/user.head.php';

$userId    = (int)$user['id'];
$sessionId = (int)(Request::get('session_id') ?? Request::post('session_id') ?? 0);

if ($sessionId <= 0) {
    Response::redirect('/user/sessions');
}

const FOLLOWUP_MAX_MESSAGES = 5;
const FOLLOWUP_WINDOW_DAYS  = 7;

/* ── Fetch session + counselor info ─────────────────────────── */
$rs = Database::search("
    SELECT s.session_id, s.user_id, s.counselor_id, s.session_datetime, s.status,
           COALESCE(u.display_name, u.username) AS counselor_name,
           u.profile_picture                    AS counselor_avatar,
           c.title                              AS counselor_title,
           c.specialty
    FROM sessions s
    JOIN counselors c ON c.counselor_id = s.counselor_id
    JOIN users u ON u.user_id = c.user_id
    WHERE s.session_id = $sessionId
      AND s.user_id    = $userId
      AND s.status     = 'completed'
    LIMIT 1
");

if (!$rs || $rs->num_rows === 0) {
    Response::redirect('/user/sessions');
}
$session = $rs->fetch_assoc();

$sessionTs = strtotime($session['session_datetime']);
$expiresTs = $sessionTs + (FOLLOWUP_WINDOW_DAYS * 86400);
$daysLeft  = max(0, (int)ceil(($expiresTs - time()) / 86400));
$isExpired = time() > $expiresTs;

/* ── Fetch messages ─────────────────────────────────────────── */
$messages = [];
$msgCount = 0;
$msgsRs = Database::search("
    SELECT sm.message_id, sm.sender_id, sm.message, sm.created_at,
           COALESCE(u.display_name, u.username) AS sender_name,
           u.profile_picture AS sender_avatar,
           u.role            AS sender_role
    FROM session_messages sm
    JOIN users u ON u.user_id = sm.sender_id
    WHERE sm.session_id = $sessionId
    ORDER BY sm.created_at ASC
");
if ($msgsRs) {
    while ($row = $msgsRs->fetch_assoc()) {
        $messages[] = $row;
    }
    $msgCount = count($messages);
}
$isLocked = $isExpired || $msgCount >= FOLLOWUP_MAX_MESSAGES;

/* ── Handle POST ─────────────────────────────────────────────── */
$sendError = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $msg = trim(Request::post('message') ?? '');

    if ($isLocked) {
        $sendError = 'This follow-up thread is closed.';
    } elseif (strlen($msg) < 1 || strlen($msg) > 1000) {
        $sendError = 'Message must be between 1 and 1000 characters.';
    } else {
        Database::setUpConnection();
        $safeMsg = Database::$connection->real_escape_string($msg);
        Database::iud("INSERT INTO session_messages (session_id, sender_id, message)
                        VALUES ($sessionId, $userId, '$safeMsg')");
        Response::redirect("/user/sessions/follow-up?session_id=$sessionId");
    }
}

/* ── Layout vars ─────────────────────────────────────────────── */
$pageTitle = 'Follow-up Thread';
$pageStyle = ['user/sessions', 'user/follow-up'];
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once __DIR__ . '/../../common/user.html.head.php'; ?>
<body>
<main class="main-container">
    <?php $activePage = 'sessions'; require_once __DIR__ . '/../../common/user.sidebar.php'; ?>

    <section class="main-content">
        <img src="/assets/img/main-content-head.svg" alt="" class="main-header-bg-image" />

        <div class="main-content-header">
            <div class="main-content-header-text">
                <h2>Follow-up Thread</h2>
                <p>Post-session messages with your counselor.</p>
            </div>
        </div>

        <div class="main-content-body">
            <div class="followup-container">

                <!-- Back -->
                <div class="back-navigation" style="margin-bottom:var(--spacing-lg);">
                    <a href="/user/sessions" class="back-btn" title="Back to Sessions">
                        <i data-lucide="chevron-left" style="width:18px;height:18px;"></i>
                    </a>
                </div>

                <!-- Thread header card -->
                <div class="followup-header-card">
                    <img src="<?= htmlspecialchars($session['counselor_avatar'] ?? '/assets/img/avatar.png') ?>"
                         class="followup-counselor-avatar" alt="Counselor" />
                    <div class="followup-header-info">
                        <p class="followup-specialty"><?= htmlspecialchars($session['specialty'] ?? '') ?></p>
                        <h3 class="followup-counselor-name"><?= htmlspecialchars($session['counselor_name']) ?></h3>
                        <p class="followup-session-date">
                            Session on <?= date('M j, Y g:i A', $sessionTs) ?>
                        </p>
                    </div>
                    <div class="followup-thread-status">
                        <div class="followup-counters">
                            <div class="followup-counter <?= $isLocked ? 'locked' : '' ?>">
                                <i data-lucide="message-square" style="width:14px;height:14px;"></i>
                                <span><?= $msgCount ?>/<?= FOLLOWUP_MAX_MESSAGES ?> messages</span>
                            </div>
                            <?php if (!$isExpired): ?>
                            <div class="followup-counter <?= $daysLeft <= 1 ? 'urgent' : '' ?>">
                                <i data-lucide="clock" style="width:14px;height:14px;"></i>
                                <span><?= $daysLeft ?> day<?= $daysLeft !== 1 ? 's' : '' ?> left</span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($isLocked): ?>
                            <span class="followup-badge locked-badge">
                                <i data-lucide="lock" style="width:12px;height:12px;"></i>
                                Thread Closed
                            </span>
                        <?php else: ?>
                            <span class="followup-badge open-badge">
                                <i data-lucide="unlock" style="width:12px;height:12px;"></i>
                                Open
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Messages area -->
                <div class="followup-messages <?= empty($messages) ? 'empty' : '' ?>">
                    <?php if (empty($messages)): ?>
                        <div class="followup-empty-state">
                            <i data-lucide="message-circle" style="width:40px;height:40px;color:var(--color-text-muted);display:block;margin:0 auto var(--spacing-md);"></i>
                            <p>No messages yet. Start the conversation below.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($messages as $msg):
                            $isMe = (int)$msg['sender_id'] === $userId;
                            $avatar = !empty($msg['sender_avatar']) ? $msg['sender_avatar'] : '/assets/img/avatar.png';
                            $timeStr = date('M j, g:i A', strtotime($msg['created_at']));
                        ?>
                        <div class="followup-message <?= $isMe ? 'message-mine' : 'message-theirs' ?>">
                            <?php if (!$isMe): ?>
                            <img src="<?= htmlspecialchars($avatar) ?>" class="message-avatar" alt="" />
                            <?php endif; ?>
                            <div class="message-bubble-wrap">
                                <span class="message-sender">
                                    <?= $isMe ? 'You' : htmlspecialchars($msg['sender_name']) ?>
                                </span>
                                <div class="message-bubble"><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                                <span class="message-time"><?= $timeStr ?></span>
                            </div>
                            <?php if ($isMe): ?>
                            <img src="<?= htmlspecialchars($avatar) ?>" class="message-avatar" alt="" />
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Compose area / Locked state -->
                <?php if ($isLocked): ?>
                <div class="followup-locked">
                    <i data-lucide="lock" style="width:24px;height:24px;color:var(--color-text-muted);"></i>
                    <div>
                        <p class="followup-locked-title">Follow-up window closed</p>
                        <p class="followup-locked-sub">
                            <?= $isExpired
                                ? 'The 7-day follow-up period has ended.'
                                : 'The 5-message limit has been reached.' ?>
                        </p>
                    </div>
                    <a href="/user/counselors?tab=find" class="btn btn-primary" style="font-size:var(--font-size-sm);">
                        Book Another Session →
                    </a>
                </div>
                <?php else: ?>
                <form method="POST" class="followup-compose">
                    <input type="hidden" name="session_id" value="<?= $sessionId ?>">
                    <?php if ($sendError): ?>
                        <p class="followup-error"><?= htmlspecialchars($sendError) ?></p>
                    <?php endif; ?>
                    <div class="followup-input-row">
                        <textarea name="message" class="followup-textarea"
                                  placeholder="Write a follow-up message…"
                                  maxlength="1000" rows="3" required></textarea>
                        <button type="submit" class="btn btn-primary followup-send-btn">
                            <i data-lucide="send" style="width:16px;height:16px;"></i>
                        </button>
                    </div>
                    <p class="followup-hint">
                        <?= FOLLOWUP_MAX_MESSAGES - $msgCount ?> message<?= (FOLLOWUP_MAX_MESSAGES - $msgCount) !== 1 ? 's' : '' ?> remaining
                        · <?= $daysLeft ?> day<?= $daysLeft !== 1 ? 's' : '' ?> left
                    </p>
                </form>
                <?php endif; ?>

            </div>
        </div>
    </section>
</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
lucide.createIcons();
// Scroll messages to bottom
const msgs = document.querySelector('.followup-messages');
if (msgs) msgs.scrollTop = msgs.scrollHeight;
</script>
<script src="/assets/js/auth/user-profile.js"></script>
</body>
</html>
