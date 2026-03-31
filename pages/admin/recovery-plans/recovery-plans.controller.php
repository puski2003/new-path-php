<?php
require_once __DIR__ . '/recovery-plans.model.php';
$activeTab = Request::get('tab') ?? 'pre-built';
$filters = ['search' => Request::get('search') ?? '', 'category' => Request::get('category') ?? 'all', 'status' => Request::get('status') ?? 'all'];
$plans = RecoveryPlansAdminModel::getTemplates($filters);
$questions = RecoveryPlansAdminModel::getQuestions($filters);
