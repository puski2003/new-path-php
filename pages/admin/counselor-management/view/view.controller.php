<?php
require_once __DIR__ . '/view.model.php';

$applicationId = (int) (Request::get('id') ?? 0);
$application = ViewApplicationModel::getApplication($applicationId);
$error = $application ? '' : 'Application not found.';
