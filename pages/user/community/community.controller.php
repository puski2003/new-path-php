<?php
$userId = (int)$user['id'];

$searchQuery = Request::get('q') ?? '';
$scope = Request::get('scope') ?? 'all';
if (!in_array($scope, ['all', 'mine', 'trending'], true)) {
    $scope = 'all';
}

$posts = CommunityModel::getPosts($userId, [
    'q' => $searchQuery,
    'scope' => $scope,
]);

$pageTitle = 'Community';
$pageStyle = ['user/dashboard', 'user/community'];
