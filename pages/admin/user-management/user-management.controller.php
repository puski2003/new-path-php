<?php
require_once __DIR__ . '/user-management.model.php';

$filters = [
    'role' => Request::get('role') ?? 'all',
    'status' => Request::get('status') ?? 'all',
    'dateJoined' => Request::get('dateJoined') ?? '',
    'engagement' => Request::get('engagement') ?? 'all',
    'search' => Request::get('search') ?? '',
];

$users = UserManagementModel::getUsers($filters);
