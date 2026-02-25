<?php

/**
 * /auth/logout — clears the JWT cookie and redirects to login
 * Only accepts POST (the sidebar logout button uses a form).
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::redirect('/auth/login');
}

Auth::clearTokenCookie();
Response::redirect('/auth/login');
