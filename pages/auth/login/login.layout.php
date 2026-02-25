<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Path — Login</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/auth/login.css">
</head>

<body>
    <div class="login-container">

        <header class="login-header">
            <div class="logo-container">
                <img src="/assets/img/logo.svg" alt="NewPath Logo" class="logo">
                <span class="logo-text">New<br>Path</span>
            </div>
        </header>

        <div class="login-title">
            <h1>Welcome back</h1>
            <p>Log in to your account.</p>
        </div>

        <?php if ($error !== null): ?>
            <div class="alert alert--error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="form-wrapper">
            <div class="form-container">

                <div class="login-left">
                    <form class="login-form" method="POST" action="">
                        <!-- CSRF would go here in a future iteration -->

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-input"
                                placeholder="Enter your email"
                                value="<?= htmlspecialchars(Request::post('email') ?? '') ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="password-input-wrapper">
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-input"
                                    placeholder="Enter your password"
                                    required>
                                <button type="button" class="password-toggle" id="passwordToggle" aria-label="Toggle password visibility">
                                    <svg id="eyeIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="form-options">
                            <label class="checkbox-label">
                                <input type="checkbox" name="rememberMe" id="rememberMe">
                                <span>Keep me signed in</span>
                            </label>
                            <a href="/auth/forgot-password" class="form-link">Forgot password?</a>
                        </div>

                        <button type="submit" class="btn btn--primary btn--full">Log in</button>

                        <p class="signup-prompt">
                            New here? <a href="/auth/register" class="form-link">Create account</a>
                        </p>
                    </form>
                </div>

                <div class="login-right">
                    <div class="image-container">
                        <img src="/assets/img/login-illustration.png" alt="Login Illustration" class="login-illustration">
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="/assets/js/auth/login.js" defer></script>
</body>

</html>