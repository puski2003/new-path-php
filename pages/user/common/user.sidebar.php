<?php

/**
 * User Sidebar
 * $user is already set by user.head.php when this is included.
 * Expects $activePage to be set before including this file.
 */
$currentPath = Request::path();

// Nav items: [label, href, lucide-icon-name, page-key]
$navItems = [
    ['Dashboard',      '/user/dashboard',      'home',      'dashboard'],
    ['Counselors',     '/user/counselors',      'user-star', 'counselors'],
    ['Recovery',       '/user/recovery',        'heart',     'recovery'],
    ['Community',      '/user/community',       'users',     'community'],
    ['Sessions',       '/user/sessions',        'video',     'sessions'],
    ['Post Recovery',  '/user/post-recovery',   'trophy',    'post-recovery'],
];
?>
<section class="sidebar">
    <div class="logo-container">
        <img src="/assets/img/logo.svg" alt="Logo" class="logo" />
        <h1 class="logo-text">New<br />Path</h1>
    </div>

    <div class="nav">
        <?php foreach ($navItems as [$label, $href, $icon, $pageKey]): ?>
            <a href="<?= $href ?>" class="sidebar-nav-link">
                <div class="sidebar-item <?= (isset($activePage) && $activePage === $pageKey) ? 'sidebar-item--active' : '' ?>">
                    <i data-lucide="<?= $icon ?>" class="sidebar-icon" stroke-width="1"></i>
                    <span class="sidebar-text"><?= htmlspecialchars($label) ?></span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="emergency-banner">
        <h1>Emergency Help</h1>
        <p>Instant support when you need it most.</p>
        <i data-lucide="shield-alert" stroke-width="1" class="emergency-illustration"></i>
        <a class="btn btn-primary" href="/user/help">Get Help Now</a>
    </div>

    <!-- User Info -->
    <div class="user-info" id="userMenuBtn" style="cursor: pointer;">
        <img src="<?= !empty($user['profilePictureUrl']) ? htmlspecialchars($user['profilePictureUrl']) : '/assets/img/avatar.png' ?>"
            alt="User Icon"
            class="user-icon" />
        <div class="user-details">
            <span class="user-name"><?= htmlspecialchars(explode(' ', $user['name'] ?? 'User')[0]) ?></span>
            <span class="user-role"><?= htmlspecialchars($user['role'] ?? 'User') ?></span>
        </div>
        <div class="user-menu-container">
            <i data-lucide="chevron-down" class="dropdown-icon" stroke-width="1"></i>
            <div class="user-menu-dropdown" id="userMenuDropdown">
                <button class="menu-option" id="editProfileBtn">
                    <i data-lucide="user" stroke-width="1"></i>
                    <span>Edit Profile</span>
                </button>
                <button class="menu-option" onclick="window.location.href='/auth/logout'">
                    <i data-lucide="log-out" stroke-width="1"></i>
                    <span>Logout</span>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- User Profile Modal -->
<?php require_once __DIR__ . '/user-profile-modal.php'; ?>