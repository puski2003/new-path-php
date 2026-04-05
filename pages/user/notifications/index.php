<?php

/**
 * Route: /user/notifications
 * AJAX-only endpoint for the notification bell.
 *
 * GET  ?ajax=list              — fetch latest 20 notifications + unread count
 * POST ?ajax=mark_read         — mark all as read (sets is_read=1)
 * POST ?ajax=mark_one_read     — body: {notification_id} — mark single as read
 */

require_once __DIR__ . '/../common/user.head.php';

header('Content-Type: application/json');

$userId     = (int)$user['id'];
$ajaxAction = Request::get('ajax');

switch ($ajaxAction) {

    case 'list':
        $rs = Database::search(
            "SELECT notification_id, type, title, message, link, is_read, created_at
             FROM notifications
             WHERE user_id = $userId
             ORDER BY created_at DESC
             LIMIT 20"
        );
        $items      = [];
        $unreadCount = 0;
        while ($rs && ($row = $rs->fetch_assoc())) {
            $isRead = (int)$row['is_read'] === 1;
            if (!$isRead) $unreadCount++;
            $items[] = [
                'id'        => (int)$row['notification_id'],
                'type'      => $row['type'],
                'title'     => $row['title'],
                'message'   => $row['message'],
                'link'      => $row['link'] ?: null,
                'isRead'    => $isRead,
                'createdAt' => $row['created_at'],
                'timeAgo'   => self_time_ago(strtotime($row['created_at'])),
            ];
        }
        echo json_encode(['success' => true, 'notifications' => $items, 'unread' => $unreadCount]);
        exit;

    case 'mark_read':
        Database::iud(
            "UPDATE notifications SET is_read = 1
             WHERE user_id = $userId AND is_read = 0"
        );
        echo json_encode(['success' => true]);
        exit;

    case 'mark_one_read':
        $notifId = (int)(Request::post('notification_id') ?? 0);
        if ($notifId > 0) {
            Database::iud(
                "UPDATE notifications SET is_read = 1
                 WHERE notification_id = $notifId AND user_id = $userId"
            );
        }
        echo json_encode(['success' => true]);
        exit;
}

echo json_encode(['success' => false, 'error' => 'Unknown action']);
exit;

// ------------------------------------------------------------------
function self_time_ago(int $ts): string
{
    $diff = time() - $ts;
    if ($diff < 60)     return 'just now';
    if ($diff < 3600)   return floor($diff / 60) . 'm ago';
    if ($diff < 86400)  return floor($diff / 3600) . 'h ago';
    if ($diff < 604800) return floor($diff / 86400) . 'd ago';
    return date('M j', $ts);
}
