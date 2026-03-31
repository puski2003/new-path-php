<?php
require_once __DIR__ . '/analytics.model.php';
$summary = AnalyticsModel::getSummary();
$timePeriod = Request::get('timePeriod') ?? 'lastMonth';
