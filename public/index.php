<?php

/**
 * Front Controller — public/index.php
 *
 * How it works:
 *   - Every HTTP request is rewritten here by .htaccess
 *   - The URL path maps directly to pages/<path>/index.php
 *   - If the file doesn't exist → 404
 *
 * URL examples:
 *   /auth/login              → pages/auth/login/index.php
 *   /admin/dashboard         → pages/admin/dashboard/index.php
 *   /user/sessions           → pages/user/sessions/index.php
 */

define('ROOT', dirname(__DIR__));

// Bootstrap — load env + core helpers
require_once ROOT . '/config/env.php';
require_once ROOT . '/config/database.php';
require_once ROOT . '/core/Auth.php';
require_once ROOT . '/core/Response.php';
require_once ROOT . '/core/Request.php';

// Resolve URL path → page file
$path     = Request::path();                    // e.g. "/admin/dashboard"
$path     = '/' . trim($path, '/');            // normalise leading slash
$pagePath = ROOT . '/pages' . $path . '/index.php';

// Strip query string from path (already done in Request::path)
// Clean up: remove any path traversal attempts
if (str_contains($path, '..')) {
    Response::abort(400, 'Bad Request');
}

// Default route: / → redirect to login
if ($path === '/') {
    Response::redirect('/auth/login');
}

// Serve the page if it exists
if (file_exists($pagePath)) {
    require_once $pagePath;
} else {
    // 404 — page not found
    http_response_code(404);
    require_once ROOT . '/pages/404.php';
}
