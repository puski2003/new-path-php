<?php

/**
 * /admin/dashboard — entry point
 * 1. auth guard  → sets $user
 * 2. controller  → sets $data
 * 3. layout      → renders HTML
 */
require_once ROOT . '/pages/admin/common/admin.head.php';
require_once ROOT . '/pages/admin/dashboard/dashboard.controller.php';
require_once ROOT . '/pages/admin/dashboard/dashboard.layout.php';
