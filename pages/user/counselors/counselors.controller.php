<?php

/**
 * Counselors Controller
 */

// Handle search and filters
$searchQuery = $_GET['q'] ?? '';
$selectedSpecialty = $_GET['specialty'] ?? '';
$selectedMinExperience = $_GET['minExperience'] ?? '';
$selectedMaxPrice = $_GET['maxPrice'] ?? '';
$selectedMinRating = $_GET['minRating'] ?? '';
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$limit = 12;

$params = [
    'q'             => $searchQuery,
    'specialty'     => $selectedSpecialty,
    'minExperience' => $selectedMinExperience,
    'maxPrice'      => $selectedMaxPrice,
    'minRating'     => $selectedMinRating,
    'limit'         => $limit,
    'offset'        => ($currentPage - 1) * $limit
];

// Load from model
$result      = CounselorsModel::getCounselors($params);
$counselors  = $result['data'];
$totalCount  = $result['total'];
$specialties = CounselorsModel::getSpecialties();
$totalPages  = ceil($totalCount / $limit);

// Pass variables to Layout
// ($counselors, $specialties, $searchQuery, etc. are now available to the layout)
