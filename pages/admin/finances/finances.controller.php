<?php
require_once __DIR__ . '/finances.model.php';
$filters = ['disputeStatus' => Request::get('disputeStatus') ?? 'all', 'disputeIssue' => Request::get('disputeIssue') ?? 'allIssues'];
$search = Request::get('search') ?? '';
$summary = FinancesModel::getSummary();
$disputes = FinancesModel::getDisputes($filters);
$transactions = FinancesModel::getTransactions($search);
