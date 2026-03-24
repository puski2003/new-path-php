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
            <div class="main-content-header-text"><h2>My Clients</h2><p>View and manage your clients</p></div>
        </div>
        <div class="main-content-body">
            <div class="inner-body-content row">
                <div class="column">
                    <button class="btn btn-bg-light-green filter-button" type="button"><i data-lucide="filter" class="filter-icon" stroke-width="1"></i><span>Filter</span></button>
                    <?php require __DIR__ . '/../common/counselor.searchbar.php'; ?>
                </div>
                <?php foreach ($clients as $client): ?>
                    <?php require __DIR__ . '/../common/counselor.client-card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>
<script>lucide.createIcons();</script>
</body>
</html>
