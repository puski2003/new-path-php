<?php

/**
 * Counselors Controller — handles both "My Counselors" and "Find a Counselor" tabs.
 */

$activeTab = $_GET['tab'] ?? 'my';

// Defaults for Find tab (needed even on My tab to avoid undefined-var warnings in layout)
$myCounselors        = [];
$counselors          = [];
$specialties         = [];
$searchQuery         = '';
$selectedSpecialty   = '';
$selectedMinExperience = '';
$selectedMaxPrice    = '';
$selectedMinRating   = '';
$currentPage         = 1;
$totalPages          = 1;

if ($activeTab === 'my') {
    $myCounselors = CounselorsModel::getMyCounselors((int)$user['id']);
} else {
    $searchQuery         = $_GET['q'] ?? '';
    $selectedSpecialty   = $_GET['specialty'] ?? '';
    $selectedMinExperience = $_GET['minExperience'] ?? '';
    $selectedMaxPrice    = $_GET['maxPrice'] ?? '';
    $selectedMinRating   = $_GET['minRating'] ?? '';
    $currentPage         = max(1, (int)($_GET['page'] ?? 1));
    $limit               = 12;

    $params = [
        'q'             => $searchQuery,
        'specialty'     => $selectedSpecialty,
        'minExperience' => $selectedMinExperience,
        'maxPrice'      => $selectedMaxPrice,
        'minRating'     => $selectedMinRating,
        'limit'         => $limit,
        'offset'        => ($currentPage - 1) * $limit,
    ];

    $result      = CounselorsModel::getCounselors($params);
    $counselors  = $result['data'];
    $totalCount  = $result['total'];
    $specialties = CounselorsModel::getSpecialties();
    $totalPages  = (int)ceil($totalCount / $limit);
}
