<?php

/**
 * POST /counselor/recovery-plans/ai-generate
 *
 * Receives { "prompt": "..." } JSON, calls the Gemini API, and returns a
 * structured recovery-plan JSON object ready for the create/edit form.
 *
 * Response shape (success):
 *   { ok: true, plan: { title, goal, description, startDate, endDate,
 *       shortTermGoalTitle, shortTermGoalDays, longTermGoalTitle,
 *       longTermGoalDays, phases: { "1":…, "2":…, "3":… }, notes } }
 *
 * Response shape (error):
 *   { ok: false, error: "message" }
 */

require_once __DIR__ . '/../../common/counselor.head.php';

header('Content-Type: application/json');

// ── Guard: POST only ──────────────────────────────────────────────────────────
if (!Request::isPost()) {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

// ── Parse body ────────────────────────────────────────────────────────────────
$body   = (string) file_get_contents('php://input');
$parsed = json_decode($body, true);
$prompt = trim((string) ($parsed['prompt'] ?? ''));

if ($prompt === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Prompt is required']);
    exit;
}

// ── API key check ─────────────────────────────────────────────────────────────
$apiKey = env('GEMINI_API_KEY', '');
if ($apiKey === '') {
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'AI service is not configured. Add GEMINI_API_KEY to .env']);
    exit;
}

// ── System prompt ─────────────────────────────────────────────────────────────
$systemPrompt = <<<'PROMPT'
You are an expert addiction-recovery counselor assistant. A counselor has described a recovery plan they want to create for a client.
Generate a complete, clinically appropriate recovery plan.

Return ONLY valid JSON — no markdown fences, no explanation, no extra text — with exactly this structure:
{
  "title":               "Short plan title",
  "goal":                "One-sentence primary goal",
  "description":         "2-3 sentence plan description",
  "startDate":           "YYYY-MM-DD (today)",
  "endDate":             "YYYY-MM-DD (based on duration in prompt, default 3 months)",
  "shortTermGoalTitle":  "Short-term goal text",
  "shortTermGoalDays":   30,
  "longTermGoalTitle":   "Long-term goal text",
  "longTermGoalDays":    90,
  "phases": {
    "1": {
      "name": "Stabilization",
      "tasks": [
        { "title": "task description", "type": "custom",      "recurrence": "" },
        { "title": "task description", "type": "session",     "recurrence": "weekly" },
        { "title": "task description", "type": "journal",     "recurrence": "daily" }
      ],
      "milestones": ["milestone text"]
    },
    "2": {
      "name": "Reduction",
      "tasks": [
        { "title": "task description", "type": "meditation",  "recurrence": "daily" },
        { "title": "task description", "type": "exercise",    "recurrence": "weekly" },
        { "title": "task description", "type": "session",     "recurrence": "bi-weekly" }
      ],
      "milestones": ["milestone text"]
    },
    "3": {
      "name": "Maintenance",
      "tasks": [
        { "title": "task description", "type": "custom",      "recurrence": "" },
        { "title": "task description", "type": "session",     "recurrence": "weekly" }
      ],
      "milestones": ["milestone text"]
    }
  },
  "notes": "Private counselor notes about this plan"
}

Rules:
- task.type must be one of: custom, journal, meditation, session, exercise
- task.recurrence must be one of: "" (one-time), "daily", "weekly", "bi-weekly"
- Provide 3–5 tasks per phase
- Dates must use YYYY-MM-DD format
- Keep all text concise and clinically appropriate
PROMPT;

// ── Build Gemini request ──────────────────────────────────────────────────────
$requestBody = json_encode([
    'contents' => [[
        'parts' => [[
            'text' => $systemPrompt . "\n\nCounselor's request:\n" . $prompt,
        ]],
    ]],
    'generationConfig' => [
        'temperature'     => 0.7,
        'maxOutputTokens' => 2048,
        'responseMimeType' => 'application/json',
    ],
]);

$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key='
     . urlencode($apiKey);

// ── cURL call ─────────────────────────────────────────────────────────────────
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $requestBody,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_TIMEOUT        => 30,
]);

$raw      = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

if ($curlErr || !$raw) {
    http_response_code(502);
    echo json_encode(['ok' => false, 'error' => 'Could not reach AI service. Check your internet connection.']);
    exit;
}

if ($httpCode !== 200) {
    $errBody = json_decode($raw, true);
    $errMsg  = $errBody['error']['message'] ?? 'Gemini API error (' . $httpCode . ')';
    http_response_code(502);
    echo json_encode(['ok' => false, 'error' => $errMsg]);
    exit;
}

// ── Parse Gemini response ─────────────────────────────────────────────────────
$gemini   = json_decode($raw, true);
$planText = $gemini['candidates'][0]['content']['parts'][0]['text'] ?? '';

// Strip markdown code fences in case the model ignores responseMimeType
$planText = preg_replace('/^```(?:json)?\s*/m', '', $planText);
$planText = preg_replace('/\s*```$/m', '',  $planText);
$planText = trim($planText);

$plan = json_decode($planText, true);

if (!is_array($plan) || empty($plan['title'])) {
    http_response_code(502);
    echo json_encode(['ok' => false, 'error' => 'AI returned an unreadable response. Please try again with a clearer description.']);
    exit;
}

// ── Return ────────────────────────────────────────────────────────────────────
echo json_encode(['ok' => true, 'plan' => $plan]);
exit;
