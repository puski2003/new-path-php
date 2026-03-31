<?php

/**
 * /auth/logout — clears JWT cookie and redirects to login.
 * POST only.
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::redirect('/auth/login');
}
Auth::clearTokenCookie();
Response::redirect('/auth/login');
