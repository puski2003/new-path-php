<?php
// Normalise $pageStyle to an array
$_pageStyles = [];
if (!empty($pageStyle)) {
    $_pageStyles = is_array($pageStyle) ? $pageStyle : [$pageStyle];
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'New Path') ?></title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/components/sidebar.css">
    <link rel="stylesheet" href="/assets/css/auth/user-profile.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <?php foreach ($_pageStyles as $_css): ?>
        <link rel="stylesheet" href="/assets/css/<?= htmlspecialchars($_css) ?>.css">
    <?php endforeach; ?>
</head>