<?php

/**
 * Login Controller — handles both GET and POST for /auth/login
 *
 * GET  → render login form (nothing to do, just fall through to layout)
 * POST → validate credentials, sign JWT, set cookie, redirect by role
 */

$error = null;

if (Request::isPost()) {
    $email    = Request::post('email') ?? '';
    $password = Request::post('password') ?? '';

    // Basic presence validation
    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        require_once 'login.model.php';
        $user = LoginModel::findByEmail($email);

        if ($user === null || !password_verify($password, $user['password'])) {
            $error = 'Invalid email or password.';
        } else {
            // Credentials good — sign JWT and redirect to role home
            $token = Auth::sign([
                'id'   => $user['id'],
                'name' => $user['name'],
                'role' => $user['role'],
            ]);

            Auth::setTokenCookie($token);

            $destinations = [
                'admin'     => '/admin/dashboard',
                'counselor' => '/counselor/dashboard',
                'user'      => '/user/dashboard',
            ];

            $dest = $destinations[$user['role']] ?? '/auth/login';
            Response::redirect($dest);
        }
    }
}
// If GET or validation failed → fall through to login.layout.php
// $error is available in the layout (null = no error to show)
