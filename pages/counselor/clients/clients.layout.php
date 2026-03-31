<?php $activePage = 'clients'; ?>
<!DOCTYPE html>
<html lang="en">
<?php $pageTitle = 'Counselor Clients'; $pageStyle = ['counselor/clients']; require __DIR__ . '/../common/counselor.html.head.php'; ?>
<body>
<main class="main-container theme-counselor">
    <?php require __DIR__ . '/../common/counselor.sidebar.php'; ?>
    <section class="main-content">
        <img src="/assets/img/main-content-head.svg" alt="Main Content Head background" class="main-header-bg-image" />
        <div class="main-content-header">
            <div class="main-content-header-text">
                <h2>My Clients</h2>
                <p>View and manage your clients</p>
            </div>
        </div>
        <div class="main-content-body dashboard-overflow">
            <div class="inner-body-content">
                <div class="body-column">
                    <div class="dashboard-card counselor-toolbar-card">
                        <div class="counselor-toolbar">
                            <button class="btn btn-bg-light-green filter-button" type="button">
                                <i data-lucide="filter" class="filter-icon" stroke-width="1"></i>
                                <span>Filter</span>
                            </button>
                            <?php require __DIR__ . '/../common/counselor.searchbar.php'; ?>
                        </div>
                    </div>

                    <div class="dashboard-card counselor-list-card">
                        <div class="card-header">
                            <h3>Client Directory</h3>
                        </div>
                        <div class="counselor-card-list">
                            <?php foreach ($clients as $client): ?>
                                <?php require __DIR__ . '/../common/counselor.client-card.php'; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<script>lucide.createIcons();</script>
</body>
</html>
