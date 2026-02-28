<?php

class CommunityModel
{
    public static function getPosts(int $userId, array $params = []): array
    {
        $q = trim((string)($params['q'] ?? ''));
        $scope = trim((string)($params['scope'] ?? 'all')); // all|mine|trending

        $where = "WHERE p.is_active = 1";
        if ($q !== '') {
            $sq = addslashes($q);
            $where .= " AND (p.title LIKE '%$sq%' OR p.content LIKE '%$sq%')";
        }
        if ($scope === 'mine') {
            $where .= " AND p.user_id = $userId";
        }

        $order = $scope === 'trending'
            ? "ORDER BY p.likes_count DESC, p.comments_count DESC, p.created_at DESC"
            : "ORDER BY p.created_at DESC";

        $sql = "
            SELECT
                p.post_id,
                p.user_id,
                p.title,
                p.content,
                p.image_url,
                p.post_type,
                p.is_anonymous,
                p.likes_count,
                p.comments_count,
                p.shares_count,
                p.created_at,
                COALESCE(u.display_name, CONCAT(u.first_name, ' ', u.last_name), u.username, 'User') AS display_name,
                u.username,
                u.profile_picture
            FROM community_posts p
            JOIN users u ON u.user_id = p.user_id
            $where
            $order
            LIMIT 100
        ";

        $rs = Database::search($sql);
        $posts = [];
        while ($row = $rs->fetch_assoc()) {
            $posts[] = [
                'postId' => (int)$row['post_id'],
                'userId' => (int)$row['user_id'],
                'title' => $row['title'] ?? '',
                'content' => $row['content'] ?? '',
                'imageUrl' => $row['image_url'] ?? '',
                'postType' => $row['post_type'] ?? 'general',
                'anonymous' => (bool)$row['is_anonymous'],
                'likesCount' => (int)($row['likes_count'] ?? 0),
                'commentsCount' => (int)($row['comments_count'] ?? 0),
                'sharesCount' => (int)($row['shares_count'] ?? 0),
                'createdAt' => $row['created_at'],
                'displayName' => $row['display_name'] ?? 'User',
                'username' => $row['username'] ?? 'user',
                'profilePictureUrl' => $row['profile_picture'] ?? '',
                'active' => false,
            ];
        }

        return $posts;
    }

    public static function createPost(int $userId, array $data): bool
    {
        $title = addslashes(trim((string)($data['title'] ?? '')));
        $content = addslashes(trim((string)($data['content'] ?? '')));
        $postType = addslashes(trim((string)($data['postType'] ?? 'general')));
        $isAnonymous = !empty($data['privacy']) && $data['privacy'] === 'anonymous' ? 1 : 0;
        $imageUrl = addslashes((string)($data['imageUrl'] ?? ''));

        if ($content === '') return false;

        Database::iud(
            "INSERT INTO community_posts
                (user_id, title, content, image_url, post_type, is_anonymous, is_active, likes_count, comments_count, shares_count, created_at, updated_at)
             VALUES
                ($userId, " . ($title !== '' ? "'$title'" : "NULL") . ", '$content', " . ($imageUrl !== '' ? "'$imageUrl'" : "NULL") . ", '$postType', $isAnonymous, 1, 0, 0, 0, NOW(), NOW())"
        );

        return true;
    }

    public static function updatePost(int $userId, int $postId, array $data): bool
    {
        if ($postId <= 0) return false;

        $title = addslashes(trim((string)($data['title'] ?? '')));
        $content = addslashes(trim((string)($data['content'] ?? '')));
        $postType = addslashes(trim((string)($data['postType'] ?? 'general')));
        $isAnonymous = !empty($data['privacy']) && $data['privacy'] === 'anonymous' ? 1 : 0;
        $imageUrl = isset($data['imageUrl']) ? addslashes((string)$data['imageUrl']) : null;

        if ($content === '') return false;

        $setImage = '';
        if ($imageUrl !== null) {
            $setImage = ", image_url = " . ($imageUrl !== '' ? "'$imageUrl'" : "NULL");
        }

        Database::iud(
            "UPDATE community_posts
             SET title = " . ($title !== '' ? "'$title'" : "NULL") . ",
                 content = '$content',
                 post_type = '$postType',
                 is_anonymous = $isAnonymous,
                 updated_at = NOW()
                 $setImage
             WHERE post_id = $postId
               AND user_id = $userId"
        );

        return true;
    }

    public static function deletePost(int $userId, int $postId): bool
    {
        if ($postId <= 0) return false;
        Database::iud("UPDATE community_posts SET is_active = 0, updated_at = NOW() WHERE post_id = $postId AND user_id = $userId");
        return true;
    }

    public static function incrementLike(int $postId): bool
    {
        if ($postId <= 0) return false;
        Database::iud("UPDATE community_posts SET likes_count = likes_count + 1, updated_at = NOW() WHERE post_id = $postId AND is_active = 1");
        return true;
    }

    public static function handleUpload(array $file): ?string
    {
        if (empty($file) || !isset($file['tmp_name']) || (int)($file['error'] ?? 1) !== 0) {
            return null;
        }

        if ((int)$file['size'] <= 0) return null;
        $mime = mime_content_type($file['tmp_name']) ?: '';
        if (strpos($mime, 'image/') !== 0) return null;

        $ext = pathinfo((string)$file['name'], PATHINFO_EXTENSION);
        if ($ext === '') $ext = 'jpg';
        $safeExt = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($ext));
        if ($safeExt === '') $safeExt = 'jpg';

        $filename = bin2hex(random_bytes(16)) . '.' . $safeExt;
        $targetDir = ROOT . '/public/uploads/posts';
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }

        $target = $targetDir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            return null;
        }

        return '/uploads/posts/' . $filename;
    }
}
