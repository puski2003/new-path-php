<?php

/**
 * Step 4 Onboarding Controller
 */
$error = null;

// Load risk score for recommendation UI (GET and POST)
require_once __DIR__ . '/step4.model.php';
$token   = $_COOKIE['jwt'] ?? '';
$payload = Auth::decode($token);
$riskScore = 5;
if ($payload) {
    $riskScore = Step4Model::getRiskScore((int) $payload['id']);
}

if ($riskScore >= 12) {
    $riskBand    = 'HIGH';
    $defaultPlan = 'counselor';
} elseif ($riskScore >= 8) {
    $riskBand    = 'MODERATE';
    $defaultPlan = 'system';
} else {
    $riskBand    = 'LOW';
    $defaultPlan = 'system';
}

if (Request::isPost()) {
    $selectedPlan = Request::post('selectedPlan') ?? '';

    if ($selectedPlan === '') {
        $error = 'Please select a recovery plan to continue.';
    } else {
        if (!$payload) $payload = Auth::decode($_COOKIE['jwt'] ?? '');

        if ($payload) {
            $userId = $payload['id'];

            if (Step4Model::createPlanAndComplete($userId, $selectedPlan)) {
                Response::redirect('/auth/onboarding/step5');
            } else {
                $error = 'Failed to generate plan. Please try again.';
            }
        } else {
            Response::redirect('/auth/login/user');
        }
    }
}
