<?php
$activePage         = 'clients';
$pageHeaderTitle    = 'Clients';
$pageHeaderSubtitle = 'Your client directory';
?>
<!DOCTYPE html>
<html lang="en">
<?php $pageTitle = 'Clients'; $pageStyle = ['counselor/clients']; require __DIR__ . '/../common/counselor.html.head.php'; ?>
<body>
<main class="main-container theme-counselor">
    <?php require __DIR__ . '/../common/counselor.sidebar.php'; ?>

    <section class="main-content">
        <?php require __DIR__ . '/../common/counselor.page-header.php'; ?>

        <div class="main-content-body"> 
            <!-- Toolbar: filter + search -->
            <div class="cc-toolbar">
                <?php require __DIR__ . '/../common/counselor.searchbar.php'; ?>
            </div>
            
            <!-- Client cards -->
            <?php if (!empty($clients)): ?>
                <div class="cc-clients-container">
                    <?php foreach ($clients as $client): ?>
                        <?php require __DIR__ . '/../common/counselor.client-card.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php elseif(!empty($query)): ?>
                <div class="cc-empty">
                    <i data-lucide="users" stroke-width="1"></i>
                    <p>No search results for "<?= htmlspecialchars($query) ?>"</p>
                </div>
            <?php else: ?>
                <div class="cc-empty">
                    <i data-lucide="users" stroke-width="1"></i>
                    <p>No clients yet.</p>
                    <p>Clients will appear here once they book a session with you.</p>
                </div>
            <?php endif; ?>

        </div>
    </section>
</main>
<script>lucide.createIcons();</script>
</body>
</html>
