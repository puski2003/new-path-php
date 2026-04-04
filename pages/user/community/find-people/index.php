<?php
/**
 * Route: /user/community/find-people
 */
require_once __DIR__ . '/../../common/user.head.php';
require_once __DIR__ . '/../community.model.php';
require_once __DIR__ . '/../../common/user-profile.model.php';

$userId = (int)$user['id'];
$searchQuery = Request::get('q') ?? '';
$tab = 'people';
$scope = 'all';
$posts = [];

$users = UserProfileModel::getUsers($userId, ['q' => $searchQuery]);

$dmConversations = DirectMessageModel::getConversations($userId);
$pendingRequests  = DirectMessageModel::getPendingConnectionRequests($userId);
$supportGroups    = SupportGroupModel::getUserGroups($userId);
$totalDmUnread    = DirectMessageModel::getUnreadCount($userId);
$totalGroupUnread = SupportGroupModel::getUnreadGroupMessageCount($userId);

$pageTitle = 'Find People';
$pageStyle  = ['user/dashboard', 'user/community', 'user/chat', 'user/find-people'];

$ajaxAction = Request::get('ajax');
if ($ajaxAction) {
    header('Content-Type: application/json');
    switch ($ajaxAction) {
        case 'follow':
            $targetUserId = (int)Request::post('user_id');
            echo json_encode(['success' => DirectMessageModel::followUser($userId, $targetUserId)]);
            break;
        case 'unfollow':
            $targetUserId = (int)Request::post('user_id');
            echo json_encode(['success' => DirectMessageModel::unfollowUser($userId, $targetUserId)]);
            break;
        case 'block':
            $targetUserId = (int)Request::post('user_id');
            echo json_encode(['success' => DirectMessageModel::blockUser($userId, $targetUserId)]);
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
    exit;
}

require_once __DIR__ . '/../community.layout.php';
