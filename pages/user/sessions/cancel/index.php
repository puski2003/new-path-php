<?php
/**
 * Route: /user/sessions/cancel
 *
 * Restricted to counselor and admin roles only.
 * Users cannot cancel paid sessions — they may only request a reschedule.
 */
require_once __DIR__ . '/../../common/user.head.php';

// Block user-role access
if (($user['role'] ?? '') === 'user') {
    Response::status(403);
    exit;
}
