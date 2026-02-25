<?php

/**
 * /auth/onboarding/step4 — entry point
 */
require_once __DIR__ . '/../../../../core/Auth.php';

$payload = Auth::requireRole('user');

$db = Database::getConnection();
$stmt = $db->prepare("SELECT onboarding_completed, current_onboarding_step FROM users WHERE user_id = ?");
$stmt->execute([$payload['id']]);
$u = $stmt->fetch();

if (!$u) {
    Response::redirect('/auth/login/user');
}

if ($u['onboarding_completed']) {
    Response::redirect('/user/dashboard');
}

if ($u['current_onboarding_step'] < 4) {
    Response::redirect('/auth/onboarding/step' . $u['current_onboarding_step']);
}

require_once __DIR__ . '/step4.controller.php';
require_once __DIR__ . '/step4.layout.php';
