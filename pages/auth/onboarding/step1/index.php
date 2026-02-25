<?php

/**
 * /auth/onboarding/step1 — entry point
 */
// If the user happens to have an active session, they shouldn't be here (either dashboard or proper step)
// Note: Actual restriction redirect logic is handled further as part of auth gating,
// but for step1, we allow unsigned users or unsigned users only. If signed, redirect based on onboarding_completed.
if (isset($_COOKIE['jwt'])) {
    $token = $_COOKIE['jwt'];
    $payload = Auth::decode($token);
    if ($payload) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT onboarding_completed, current_onboarding_step FROM users WHERE user_id = ?");
        $stmt->execute([$payload['id']]);
        $u = $stmt->fetch();
        if ($u) {
            if ($u['onboarding_completed']) {
                Response::redirect('/user/dashboard'); // Assuming user role
            } else if ((int)$u['current_onboarding_step'] !== 1) {
                Response::redirect('/auth/onboarding/step' . $u['current_onboarding_step']);
            }
        }
    }
}

require_once __DIR__ . '/step1.controller.php';
require_once __DIR__ . '/step1.layout.php';
