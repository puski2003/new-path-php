<!DOCTYPE html>
<html lang="en">
<?php require_once __DIR__ . '/../common/user.html.head.php'; ?>
<body>
    <main class="main-container">
        <?php $activePage = 'community'; require_once __DIR__ . '/../common/user.sidebar.php'; ?>

        <section class="main-content">
            <img src="/assets/img/main-content-head.svg" alt="Main Content Head background" class="main-header-bg-image" />

            <div class="main-content-header">
                <div class="main-content-header-text">
                    <h2>Community</h2>
                    <p>Connect with others on the same journey.</p>
                </div>

                <?php $placeholder = 'Search posts...'; require __DIR__ . '/../common/user.searchbar.php'; ?>

                <div style="width: 25%;"></div>
                <img src="/assets/img/community.svg" alt="Community" class="community-image" />
            </div>

            <div class="main-content-body">
                <div class="community-content-header">
                    <button class="btn btn-bg-light-green" type="button" aria-label="Notifications">
                        <i data-lucide="bell" stroke-width="1.8"></i>
                    </button>
                    <button class="btn btn-primary" type="button" id="openPostModalBtn">Post</button>
                </div>

                <div class="community-nav-sections">
                    <div class="filter-options">
                        <div class="filter-dropdown">
                            <a href="/user/community?scope=all<?= $searchQuery !== '' ? '&q=' . urlencode($searchQuery) : '' ?>" class="filter-btn <?= $scope === 'all' ? 'active' : '' ?>">
                                <span>All Posts</span>
                                <i data-lucide="chevron-down" class="filter-icon" stroke-width="2"></i>
                            </a>
                        </div>
                        <div class="filter-dropdown">
                            <a href="/user/community?scope=mine<?= $searchQuery !== '' ? '&q=' . urlencode($searchQuery) : '' ?>" class="filter-btn <?= $scope === 'mine' ? 'active' : '' ?>">
                                <span>My Posts</span>
                                <i data-lucide="chevron-down" class="filter-icon" stroke-width="2"></i>
                            </a>
                        </div>
                        <div class="filter-dropdown">
                            <a href="/user/community?scope=trending<?= $searchQuery !== '' ? '&q=' . urlencode($searchQuery) : '' ?>" class="filter-btn <?= $scope === 'trending' ? 'active' : '' ?>">
                                <span>Trending</span>
                                <i data-lucide="chevron-down" class="filter-icon" stroke-width="2"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="community-posts-container" id="postsContainer">
                    <?php if (empty($posts)): ?>
                        <div class="community-post"><p class="post-text">No posts found.</p></div>
                    <?php else: ?>
                        <?php foreach ($posts as $currentPost): ?>
                            <?php require __DIR__ . '/../common/user.community-post-item.php'; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <?php require __DIR__ . '/../common/user.community-post-modal.php'; ?>
    <?php require __DIR__ . '/../common/user.community-delete-confirmation-modal.php'; ?>
    <?php require __DIR__ . '/../common/user.community-chat.php'; ?>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="/assets/js/user/community.js"></script>
    <script src="/assets/js/user/chat.js"></script>
    <script src="/assets/js/auth/user-profile.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>
</body>
</html>
