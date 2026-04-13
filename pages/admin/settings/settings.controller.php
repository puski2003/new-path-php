<?php
require_once __DIR__ . '/settings.model.php';
$message = '';
if (Request::isPost()) {
    SettingsModel::save($_POST, (int) $user['id']);
    $message = 'Settings updated successfully.';
}
$settings = SettingsModel::getSettings();
$page = Pagination::sanitizePage(Request::get('page') ?? 1);
$perPage = 15;

$rolesResult = SettingsModel::getRolesPaginated([], $page, $perPage);
$roles = $rolesResult['items'];
$rolesPagination = $rolesResult['pagination'];
