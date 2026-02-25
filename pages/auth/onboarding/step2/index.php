<?php

/**
 * /auth/onboarding/step2 — entry point
 */
// Ensure user is authorized and exists
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

// Ensure strict linear progression
if ($u['current_onboarding_step'] < 2) {
    Response::redirect('/auth/onboarding/step' . $u['current_onboarding_step']);
}
if ($u['current_onboarding_step'] > 2) {
    // Optionally allow going back
    // if they are on step 3 but want to edit step 2, that's fine.
    // By rendering the form we allow that. If we wanted strict forward only:
    // Response::redirect('/auth/onboarding/step' . $u['current_onboarding_step']);
}

require_once __DIR__ . '/step2.controller.php';
require_once __DIR__ . '/step2.layout.php';
