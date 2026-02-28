<?php

/**
 * User head guard — include this at the top of every user page index.php.
 *
 * What it does:
 *   1. Verifies the JWT cookie exists and is valid
 *   2. Confirms the role is 'user'
 *   3. Sets $user = ['id', 'name', 'role', 'iat', 'exp']
 *   4. Redirects to /auth/login on any failure
 *
 * After this file is required, $user is available everywhere on the page.
 */
$user = Auth::requireRole('user');

// Normalise $pageStyle to an array
$_pageStyles = [];
if (!empty($pageStyle)) {
    $_pageStyles = is_array($pageStyle) ? $pageStyle : [$pageStyle];
}
