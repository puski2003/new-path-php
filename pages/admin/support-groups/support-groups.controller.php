<?php
require_once __DIR__ . '/support-groups.model.php';
$filters = ['type' => Request::get('type') ?? 'all', 'status' => Request::get('status') ?? 'all', 'search' => Request::get('search') ?? ''];
$groups = SupportGroupsModel::getGroups($filters);
$appointments = SupportGroupsModel::getUpcomingSessions();
