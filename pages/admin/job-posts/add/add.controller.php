<?php
require_once __DIR__ . '/add.model.php';

$error = '';
$success = '';
if (Request::isPost()) {
    $required = ['title', 'company', 'category', 'jobType', 'location', 'description'];
    foreach ($required as $field) {
        if (trim((string) ($_POST[$field] ?? '')) === '') {
            $error = 'Please fill in all required fields.';
            break;
        }
    }

    if ($error === '' && AddJobPostModel::create($_POST, (int) $user['id'])) {
        $success = 'Job post created successfully.';
        $_POST = [];
    }
}
