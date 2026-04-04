<?php
$navItems = [
    ['Today',           '/counselor/dashboard',       'home',           'dashboard'],
    ['Schedule',        '/counselor/sessions',        'calendar-days',  'sessions'],
    ['Clients',         '/counselor/clients',         'heart-pulse',    'clients'],
    ['Recovery Plans',  '/counselor/recovery-plans',  'clipboard-plus', 'recovery'],
    ['Profile',         '/counselor/profile',         'user-round',     'profile'],
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
                    <i data-lucide="<?= htmlspecialchars($icon) ?>" class="sidebar-icon" stroke-width="1"></i>
                    <span class="sidebar-text"><?= htmlspecialchars($label) ?></span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="user-info" id="counselorInfoClick">
        <img src="<?= htmlspecialchars($currentCounselor['profilePictureUrl'] ?? '/assets/img/avatar.png') ?>" alt="Counselor Icon" class="user-icon" />
        <div class="user-details">
            <span class="user-name"><?= htmlspecialchars(explode(' ', $currentCounselor['displayName'] ?? 'Counselor')[0]) ?></span>
            <span class="user-role"><?= htmlspecialchars($currentCounselor['title'] ?? 'Counselor') ?></span>
        </div>
        <div class="user-menu-container">
            <i data-lucide="chevron-down" class="dropdown-icon" stroke-width="1"></i>
            <div class="user-menu-dropdown" id="counselorMenuDropdown">
                <button class="menu-option" id="editCounselorProfileBtn" type="button">
                    <i data-lucide="user" stroke-width="1"></i>
                    <span>Edit Profile</span>
                </button>
                <button class="menu-option" id="counselorLogoutBtn" type="button">
                    <i data-lucide="log-out" stroke-width="1"></i>
                    <span>Logout</span>
                </button>
            </div>
        </div>
    </div>
</section>

<form id="counselorLogoutForm" method="POST" action="/auth/logout" style="display: none;"></form>

<?php require_once __DIR__ . '/counselor-profile-modal.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const userInfo = document.getElementById('counselorInfoClick');
    const dropdown = document.getElementById('counselorMenuDropdown');
    const editBtn = document.getElementById('editCounselorProfileBtn');
    const logoutBtn = document.getElementById('counselorLogoutBtn');
    const logoutForm = document.getElementById('counselorLogoutForm');

    userInfo?.addEventListener('click', (event) => {
        event.stopPropagation();
        dropdown?.classList.toggle('show');
    });

    editBtn?.addEventListener('click', (event) => {
        event.stopPropagation();
        dropdown?.classList.remove('show');
        openCounselorProfileModal();
    });

    logoutBtn?.addEventListener('click', (event) => {
        event.stopPropagation();
        logoutForm?.submit();
    });

    document.addEventListener('click', (event) => {
        if (userInfo && dropdown && !userInfo.contains(event.target)) {
            dropdown.classList.remove('show');
        }
    });
});
</script>
