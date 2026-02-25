<?php

/**
 * Counselor head guard — include this at the top of every counselor page index.php.
 *
 * What it does:
 *   1. Verifies the JWT cookie exists and is valid
 *   2. Confirms the role is 'counselor'
 *   3. Sets $user = ['id', 'name', 'role', 'iat', 'exp']
 *   4. Redirects to /auth/login on any failure
 *
 * After this file is required, $user is available everywhere on the page.
 */
$user = Auth::requireRole('counselor');
