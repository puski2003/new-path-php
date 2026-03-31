<?php
$pageTitle = $pageTitle ?? 'Admin';
$extraCss = $extraCss ?? [];
$extraJs = $extraJs ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - New Path</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/admin/layout.css">
    <link rel="stylesheet" href="/assets/css/admin/components/sidebar.css">
    <link rel="stylesheet" href="/assets/css/admin/components/button.css">
    <link rel="stylesheet" href="/assets/css/admin/components/dropdown.css">
    <link rel="stylesheet" href="/assets/css/admin/components/searchbar.css">
    <link rel="stylesheet" href="/assets/css/admin/components/table.css">
    <link rel="stylesheet" href="/assets/css/admin/components/table-action-button.css">
    <link rel="stylesheet" href="/assets/css/admin/components/summary-card.css">
    <link rel="stylesheet" href="/assets/css/admin/components/data-card.css">
    <link rel="stylesheet" href="/assets/css/admin/components/tab-nav.css">
    <link rel="stylesheet" href="/assets/css/admin/components/icon-button.css">
    <link rel="stylesheet" href="/assets/css/admin/components/pending-submission.css">
    <link rel="stylesheet" href="/assets/css/admin/components/content-management.css">
    <link rel="stylesheet" href="/assets/css/admin/components/recovery-plans.css">
    <link rel="stylesheet" href="/assets/css/admin/components/policy-card.css">
    <link rel="stylesheet" href="/assets/css/admin/components/alert-item.css">
    <link rel="stylesheet" href="/assets/css/admin/components/settings.css">
    <link rel="stylesheet" href="/assets/css/admin/components/session-appointments.css">
    <link rel="stylesheet" href="/assets/css/admin/components/date-input.css">
    <link rel="stylesheet" href="/assets/css/admin/components/text-input.css">
    <link rel="stylesheet" href="/assets/css/admin/components/toggle-switch.css">
    <link rel="stylesheet" href="/assets/css/admin/components/file-upload.css">
    <script src="/assets/js/components/alert.js" defer></script>
    <?php foreach ($extraCss as $css): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
    <?php endforeach; ?>
</head>
<body>
