<!DOCTYPE html>
<html lang="en">
<?php require_once __DIR__ . '/../common/user.html.head.php'; ?>

<body>
    <main class="main-container">
        <?php $activePage = 'post-recovery';
        require_once __DIR__ . '/../common/user.sidebar.php'; ?>

        <section class="main-content">
            <img src="/assets/img/main-content-head.svg"
                alt="Main Content Head background"
                class="main-header-bg-image" />

            <div class="main-content-header">
                <div class="main-content-header-text">
                    <h2>Post Recovery</h2>
                    <p>Start a new life by finding opportunities.</p>
                </div>

                <?php $placeholder = 'Search Jobs...';
                require __DIR__ . '/../common/user.searchbar.php'; ?>

                <div style="width: 25%"></div>
            </div>

            <div class="main-content-body">
                <div class="post-recovery-container">
                    <div class="filter-section">
                        <button class="btn btn-bg-light-green filter-button" type="button">
                            <i data-lucide="filter" class="filter-icon" stroke-width="1.8"></i>
                            <span>Filter</span>
                        </button>
                        <a class="btn btn-primary my-job-button <?= $onlySaved ? 'active-filter' : '' ?>" href="/user/post-recovery?my=1<?= $searchQuery !== '' ? '&q=' . urlencode($searchQuery) : '' ?>">
                            <span>My Job</span>
                        </a>
                    </div>

                    <div class="job-cards-container">
                        <?php if (!empty($jobPosts)): ?>
                            <?php foreach ($jobPosts as $job): ?>
                                <?php require __DIR__ . '/../common/user.job-card.php'; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-jobs-message">
                                <h3>No job opportunities available</h3>
                                <p>Check back later for new job postings.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php
                            $queryBase = [
                                'q' => $searchQuery,
                                'my' => $onlySaved ? '1' : '0',
                            ];
                            ?>
                            <?php if ($currentPage > 1): ?>
                                <?php $prevQuery = http_build_query(array_merge($queryBase, ['page' => $currentPage - 1])); ?>
                                <a class="pagination-btn pagination-prev" href="/user/post-recovery?<?= $prevQuery ?>">
                                    <i data-lucide="arrow-left" stroke-width="1.8"></i>
                                </a>
                            <?php else: ?>
                                <button class="pagination-btn pagination-prev" disabled>
                                    <i data-lucide="arrow-left" stroke-width="1.8"></i>
                                </button>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php if ($i === $currentPage): ?>
                                    <button class="pagination-btn pagination-number active"><?= $i ?></button>
                                <?php else: ?>
                                    <?php $pageQuery = http_build_query(array_merge($queryBase, ['page' => $i])); ?>
                                    <a class="pagination-btn pagination-number" href="/user/post-recovery?<?= $pageQuery ?>"><?= $i ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <?php $nextQuery = http_build_query(array_merge($queryBase, ['page' => $currentPage + 1])); ?>
                                <a class="pagination-btn pagination-next" href="/user/post-recovery?<?= $nextQuery ?>">
                                    <i data-lucide="arrow-right" stroke-width="1.8"></i>
                                </a>
                            <?php else: ?>
                                <button class="pagination-btn pagination-next" disabled>
                                    <i data-lucide="arrow-right" stroke-width="1.8"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <script>
        window.postRecoveryState = {
            searchQuery: <?= json_encode($searchQuery) ?>,
            saveToggleUrl: '/user/post-recovery/save',
            allJobsUrl: '/user/post-recovery',
            myJobsUrl: '/user/post-recovery?my=1'
        };
    </script>
    <script src="/assets/js/user/post-recovery.js"></script>
    <script src="/assets/js/auth/user-profile.js"></script>
    <script src="/assets/js/user/log-urge-popup.js"></script>
    <script>
        if (typeof lucide !== 'undefined') lucide.createIcons();
    </script>
</body>

</html>

