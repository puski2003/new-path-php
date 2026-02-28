<!DOCTYPE html>
<html lang="en">
<?php
$pageTitle = "Counselors";
$pageStyle = ["user/dashboard", "user/counselors"];
require_once __DIR__ . '/../common/user.html.head.php';
?>

<body>
    <main class="main-container">
        <?php
        $activePage = 'counseling';
        require_once __DIR__ . '/../common/user.sidebar.php';
        ?>

        <section class="main-content">
            <img src="/assets/img/main-content-head.svg"
                alt="Main Content Head background"
                class="main-header-bg-image" />

            <div class="main-content-header">
                <div class="main-content-header-text">
                    <h2>Counselors</h2>
                    <p>Your path to guidance starts here.</p>
                </div>

                <?php
                $placeholder = "Search counselors...";
                require __DIR__ . '/../common/user.searchbar.php';
                ?>

                <div style="width: 25%"></div>
                <img src="/assets/img/counselor-header.svg"
                    alt="Counselors"
                    class="counselors-image" />
            </div>

            <div class="main-content-body">
                <div class="counselor-cards-container">
                    <div class="">
                        <button class="btn btn-bg-light-green filter-button" onclick="toggleFilters()">
                            <i data-lucide="filter" stroke-width="1" class="filter-icon"></i>
                            <span>Filter</span>
                        </button>
                    </div>

                    <!-- Filter Panel (Hidden by default) -->
                    <div id="filterPanel" class="filter-panel" style="display: none;">
                        <form action="/user/counselors" method="GET" class="filter-form-inline">
                            <input type="hidden" name="q" id="searchQueryHidden" value="<?= htmlspecialchars($searchQuery) ?>" />

                            <div class="filter-group">
                                <label for="specialty">Specialty</label>
                                <select name="specialty" id="specialty" class="filter-select">
                                    <option value="">All Specialties</option>
                                    <?php foreach ($specialties as $spec): ?>
                                        <option value="<?= htmlspecialchars($spec) ?>" <?= ($selectedSpecialty === $spec) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($spec) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="minExperience">Min Experience</label>
                                <input type="number" name="minExperience" id="minExperience"
                                    value="<?= htmlspecialchars($selectedMinExperience) ?>" placeholder="Years" min="0" max="50" class="filter-input" />
                            </div>

                            <div class="filter-group">
                                <label for="maxPrice">Max Price (Rs)</label>
                                <input type="number" name="maxPrice" id="maxPrice"
                                    value="<?= htmlspecialchars($selectedMaxPrice) ?>" placeholder="Any" min="0" step="500" class="filter-input" />
                            </div>

                            <div class="filter-group">
                                <label for="minRating">Min Rating</label>
                                <select name="minRating" id="minRating" class="filter-select">
                                    <option value="">Any</option>
                                    <option value="4" <?= ($selectedMinRating == '4') ? 'selected' : '' ?>>4+ Stars</option>
                                    <option value="3" <?= ($selectedMinRating == '3') ? 'selected' : '' ?>>3+ Stars</option>
                                </select>
                            </div>

                            <div class="filter-actions">
                                <button type="submit" class="btn btn-primary">Apply</button>
                                <a href="/user/counselors" class="btn btn-secondary">Clear</a>
                            </div>
                        </form>
                    </div>

                    <!-- Counselor Cards -->
                    <?php if (empty($counselors)): ?>
                        <div class="no-results-message">
                            <p>No counselors found. Try adjusting your search or filters.</p>
                            <a href="/user/counselors" class="btn btn-primary">View All Counselors</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($counselors as $counselor): ?>
                            <?php require __DIR__ . '/../common/user.counselor-card.php'; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php
                            // Build query params string to persist filters
                            $queryParams = http_build_query([
                                'q' => $searchQuery,
                                'specialty' => $selectedSpecialty,
                                'minExperience' => $selectedMinExperience,
                                'maxPrice' => $selectedMaxPrice,
                                'minRating' => $selectedMinRating
                            ]);
                            ?>

                            <?php if ($currentPage > 1): ?>
                                <a href="/user/counselors?page=<?= $currentPage - 1 ?>&<?= $queryParams ?>"
                                    class="pagination-btn pagination-prev">
                                    <i data-lucide="arrow-left" stroke-width="1"></i>
                                </a>
                            <?php else: ?>
                                <button class="pagination-btn pagination-prev" disabled>
                                    <i data-lucide="arrow-left" stroke-width="1"></i>
                                </button>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php if ($i == $currentPage): ?>
                                    <button class="pagination-btn pagination-number active"><?= $i ?></button>
                                <?php else: ?>
                                    <a href="/user/counselors?page=<?= $i ?>&<?= $queryParams ?>"
                                        class="pagination-btn pagination-number"><?= $i ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <a href="/user/counselors?page=<?= $currentPage + 1 ?>&<?= $queryParams ?>"
                                    class="pagination-btn pagination-next">
                                    <i data-lucide="arrow-right" stroke-width="1"></i>
                                </a>
                            <?php else: ?>
                                <button class="pagination-btn pagination-next" disabled>
                                    <i data-lucide="arrow-right" stroke-width="1"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <script>
        function toggleFilters() {
            var filterPanel = document.getElementById('filterPanel');
            filterPanel.style.display = filterPanel.style.display === 'none' ? 'flex' : 'none';
        }

        // Make searchbar work with filters
        document.addEventListener('DOMContentLoaded', function() {
            var searchInput = document.querySelector('.search-input');
            var searchButton = document.querySelector('.search-button');

            if (searchInput && searchButton) {
                // Set initial value from server
                searchInput.value = <?= json_encode($searchQuery) ?>;

                // Handle search on button click
                searchButton.addEventListener('click', function() {
                    performSearch();
                });

                // Handle search on Enter key
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        performSearch();
                    }
                });
            }

            function performSearch() {
                var query = searchInput.value;
                var url = '/user/counselors?q=' + encodeURIComponent(query);

                // Preserve existing filters
                var specialty = document.getElementById('specialty');
                if (specialty && specialty.value) url += '&specialty=' + encodeURIComponent(specialty.value);

                var minExp = document.getElementById('minExperience');
                if (minExp && minExp.value) url += '&minExperience=' + minExp.value;

                var maxPrice = document.getElementById('maxPrice');
                if (maxPrice && maxPrice.value) url += '&maxPrice=' + maxPrice.value;

                var minRating = document.getElementById('minRating');
                if (minRating && minRating.value) url += '&minRating=' + minRating.value;

                window.location.href = url;
            }

            // Show filter panel if any filters are active
            <?php if (!empty($selectedSpecialty) || !empty($selectedMinExperience) || !empty($selectedMaxPrice) || !empty($selectedMinRating)): ?>
                document.getElementById('filterPanel').style.display = 'flex';
            <?php endif; ?>
        });
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
    <script src="/assets/js/user/counselors.js"></script>
    <script src="/assets/js/auth/user-profile.js"></script>

</body>

</html>