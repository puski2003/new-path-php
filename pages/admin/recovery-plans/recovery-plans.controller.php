<?php
require_once __DIR__ . '/recovery-plans.model.php';
$activeTab = Request::get('tab') ?? 'pre-built';
$filters = ['search' => Request::get('search') ?? '', 'category' => Request::get('category') ?? 'all', 'status' => Request::get('status') ?? 'all'];
$page = Pagination::sanitizePage(Request::get('page') ?? 1);
$perPage = 15;

$plansResult = RecoveryPlansAdminModel::getTemplatesPaginated($filters, $page, $perPage);
$plans = $plansResult['items'];
$plansPagination = $plansResult['pagination'];
$questionsResult = RecoveryPlansAdminModel::getQuestionsPaginated($filters, $page, $perPage);
$questions = $questionsResult['items'];
$questionsPagination = $questionsResult['pagination'];
