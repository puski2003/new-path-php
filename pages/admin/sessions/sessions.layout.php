<?php
$pageTitle = 'Sessions';
require_once __DIR__ . '/../common/admin.html.head.php';

$statusColors = [
    'scheduled'   => '#3b82f6',
    'confirmed'   => '#10b981',
    'in_progress' => '#f59e0b',
    'completed'   => '#6b7280',
    'cancelled'   => '#ef4444',
];
?>
<main class="admin-main-container">
    <?php require_once __DIR__ . '/../common/admin.sidebar.php'; ?>
    <section class="admin-main-content">
        <h1>Sessions</h1>

        <!-- Search -->
        <div class="admin-sub-container-2">
            <form method="GET" class="admin-sub-container-1" style="justify-content:space-between;align-items:center;">
                <h2>All Sessions (<?= $totalCount ?>)</h2>
                <div class="admin-sub-container-1">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search user, counselor, status">
                    <button class="admin-button admin-button--secondary">Search</button>
                    <?php if ($search !== ''): ?>
                        <a href="/admin/sessions" class="admin-button admin-button--secondary">Clear</a>
                    <?php endif; ?>
                </div>
            </form>

            <!-- Table -->
            <table class="admin-table">
                <thead class="admin-table-header">
                    <tr class="admin-table-row">
                        <th class="admin-table-th">#</th>
                        <th class="admin-table-th">Date &amp; Time</th>
                        <th class="admin-table-th">User</th>
                        <th class="admin-table-th">Counselor</th>
                        <th class="admin-table-th">Duration</th>
                        <th class="admin-table-th">Status</th>
                        <th class="admin-table-th">Meet Link</th>
                        <th class="admin-table-th">Meeting Details</th>
                    </tr>
                </thead>
                <tbody class="admin-table-body">
                    <?php if (empty($sessions)): ?>
                        <tr class="admin-table-row">
                            <td class="admin-table-td" colspan="8" style="text-align:center;color:#999;">No sessions found.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($sessions as $i => $s): ?>
                        <tr class="admin-table-row <?= $i % 2 === 0 ? 'admin-table-row--even' : 'admin-table-row--odd' ?>">
                            <td class="admin-table-td"><?= (int)$s['session_id'] ?></td>
                            <td class="admin-table-td"><?= htmlspecialchars(date('M j, Y g:i A', strtotime($s['session_datetime']))) ?></td>
                            <td class="admin-table-td">
                                <?= htmlspecialchars($s['user_name']) ?><br>
                                <small style="color:#999;"><?= htmlspecialchars($s['user_email']) ?></small>
                            </td>
                            <td class="admin-table-td">
                                <?= htmlspecialchars($s['counselor_name']) ?><br>
                                <small style="color:#999;"><?= htmlspecialchars($s['counselor_email']) ?></small>
                            </td>
                            <td class="admin-table-td"><?= (int)$s['duration_minutes'] ?> min</td>
                            <td class="admin-table-td">
                                <span style="
                                    display:inline-block;padding:2px 10px;border-radius:12px;font-size:.8rem;font-weight:600;
                                    background:<?= $statusColors[$s['status']] ?? '#6b7280' ?>22;
                                    color:<?= $statusColors[$s['status']] ?? '#6b7280' ?>;
                                ">
                                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $s['status']))) ?>
                                </span>
                            </td>
                            <td class="admin-table-td">
                                <?php if (!empty($s['meeting_link'])): ?>
                                    <a href="<?= htmlspecialchars($s['meeting_link']) ?>" target="_blank" style="color:#4CAF50;font-size:.85rem;">
                                        Join Meeting
                                    </a>
                                <?php else: ?>
                                    <span style="color:#bbb;font-size:.85rem;">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="admin-table-td">
                                <?php if (!empty($s['meet_space_name'])): ?>
                                    <button
                                        class="admin-button admin-button--secondary"
                                        style="font-size:.8rem;padding:4px 10px;"
                                        onclick="loadMeetingDetails(<?= (int)$s['session_id'] ?>)"
                                    >
                                        View Details
                                    </button>
                                <?php else: ?>
                                    <span style="color:#bbb;font-size:.85rem;">No space</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div style="display:flex;gap:8px;margin-top:16px;align-items:center;">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <a
                            href="?page=<?= $p ?>&search=<?= urlencode($search) ?>"
                            style="padding:4px 12px;border-radius:6px;text-decoration:none;
                                   background:<?= $p === $page ? '#4CAF50' : '#f0f0f0' ?>;
                                   color:<?= $p === $page ? '#fff' : '#333' ?>;"
                        ><?= $p ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<!-- Meeting Details Modal -->
<div id="meetModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;padding:32px;max-width:620px;width:90%;max-height:85vh;overflow-y:auto;position:relative;">
        <button onclick="closeMeetModal()" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:1.4rem;cursor:pointer;color:#999;">&times;</button>
        <h2 style="margin-top:0;">Meeting Details</h2>
        <div id="meetModalBody">
            <p style="color:#999;">Loading...</p>
        </div>
    </div>
</div>

<script>
function loadMeetingDetails(sessionId) {
    var modal = document.getElementById('meetModal');
    var body  = document.getElementById('meetModalBody');
    modal.style.display = 'flex';
    body.innerHTML = '<p style="color:#999;">Loading...</p>';

    fetch('/admin/sessions?action=meeting_details&session_id=' + sessionId, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.error) {
            body.innerHTML = '<p style="color:#e53e3e;">' + data.error + '</p>';
            return;
        }
        renderMeetingDetails(body, data);
    })
    .catch(function() {
        body.innerHTML = '<p style="color:#e53e3e;">Failed to load meeting details.</p>';
    });
}

function renderMeetingDetails(el, data) {
    var s   = data.session   || {};
    var sp  = data.space     || {};
    var cfg = sp.config      || {};
    var recs = data.conferenceRecords || [];

    var statusMap = {
        OPEN: 'Open (anyone can join)',
        TRUSTED: 'Trusted (org + contacts)',
        RESTRICTED: 'Restricted (invited only)',
    };

    var html = '<div style="display:grid;gap:8px;margin-bottom:20px;">'
        + row('Session', '#' + s.session_id + ' — ' + s.user_name + ' with ' + s.counselor_name)
        + row('Date', s.session_datetime)
        + row('Duration', s.duration_minutes + ' min')
        + row('Status', s.status)
        + '</div>';

    if (sp.meetingUri) {
        html += '<div style="display:grid;gap:8px;margin-bottom:20px;">'
            + '<h3 style="margin:0 0 8px;">Meet Space</h3>'
            + row('Link', '<a href="' + sp.meetingUri + '" target="_blank" style="color:#4CAF50;">' + sp.meetingUri + '</a>')
            + row('Space Name', sp.name || '—')
            + row('Access Type', statusMap[cfg.accessType] || cfg.accessType || '—')
            + row('Code', sp.meetingCode || '—')
            + '</div>';
    }

    if (recs.length === 0) {
        html += '<p style="color:#999;font-size:.9rem;">No conference records yet — the meeting has not been joined.</p>';
    } else {
        html += '<h3 style="margin-bottom:8px;">Conference Records (' + recs.length + ')</h3>'
            + '<div style="display:grid;gap:10px;">';
        recs.forEach(function(r) {
            var started = r.startTime ? new Date(r.startTime).toLocaleString() : '—';
            var ended   = r.endTime   ? new Date(r.endTime).toLocaleString()   : '<strong style="color:#f59e0b;">In progress</strong>';
            html += '<div style="border:1px solid #e5e7eb;border-radius:8px;padding:12px;">'
                + row('Started', started)
                + row('Ended', ended)
                + '</div>';
        });
        html += '</div>';
    }

    el.innerHTML = html;
}

function row(label, value) {
    return '<div style="display:flex;gap:12px;font-size:.9rem;">'
        + '<span style="color:#6b7280;width:110px;flex-shrink:0;">' + label + '</span>'
        + '<span>' + value + '</span>'
        + '</div>';
}

function closeMeetModal() {
    document.getElementById('meetModal').style.display = 'none';
}

document.getElementById('meetModal').addEventListener('click', function(e) {
    if (e.target === this) closeMeetModal();
});
</script>

<?php require_once __DIR__ . '/../common/admin.footer.php'; ?>
</body>
</html>
