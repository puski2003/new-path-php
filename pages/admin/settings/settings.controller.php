<?php
require_once __DIR__ . '/settings.model.php';
$message = '';
if (Request::isPost()) {
    SettingsModel::save($_POST, (int) $user['id']);
    $message = 'Settings updated successfully.';
}
$settings = SettingsModel::getSettings();
$roles = SettingsModel::getRoles();
