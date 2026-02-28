<?php
$postId = (int)($currentPost['postId'] ?? 0);
$postUserId = (int)($currentPost['userId'] ?? 0);
$isOwner = ((int)($user['id'] ?? 0) === $postUserId);
$avatar = !empty($currentPost['profilePictureUrl']) ? $currentPost['profilePictureUrl'] : '/assets/img/avatar.png';
$displayName = !empty($currentPost['anonymous']) ? 'Anonymous User' : ($currentPost['displayName'] ?? ($currentPost['username'] ?? 'User'));
$createdAt = !empty($currentPost['createdAt']) ? date('M d \a\t H:i', strtotime($currentPost['createdAt'])) : '';
?>
<div class="community-post" data-post-id="<?= $postId ?>">
    <div class="post-header">
        <div class="post-author">
            <img src="<?= htmlspecialchars($avatar) ?>" alt="<?= htmlspecialchars($displayName) ?>" class="author-avatar" />

            <div class="author-info">
                <h4 class="author-name"><?= htmlspecialchars($displayName) ?></h4>
                <span class="post-time"><?= htmlspecialchars($createdAt) ?></span>
            </div>
        </div>
        <div class="post-menu-container">
            <button class="post-menu-btn" data-post-id="<?= $postId ?>">
                <i data-lucide="more-horizontal" class="menu-icon"></i>
            </button>
            <div class="post-menu-dropdown" id="postMenu-<?= $postId ?>">
                <?php if ($isOwner): ?>
                    <button class="menu-option edit-post" data-post-id="<?= $postId ?>">
                        <i data-lucide="edit-2"></i>
                        <span>Edit Post</span>
                    </button>
                    <button class="menu-option delete-post-btn" data-post-id="<?= $postId ?>">
                        <i data-lucide="trash-2"></i>
                        <span>Delete Post</span>
                    </button>
                    <div class="menu-divider"></div>
                <?php endif; ?>
                <button class="menu-option report-post" data-post-id="<?= $postId ?>">
                    <i data-lucide="flag"></i>
                    <span>Report Post</span>
                </button>
                <button class="menu-option save-post" data-post-id="<?= $postId ?>">
                    <i data-lucide="bookmark"></i>
                    <span>Save Post</span>
                </button>
                <button class="menu-option share-post" data-post-id="<?= $postId ?>">
                    <i data-lucide="share"></i>
                    <span>Share Post</span>
                </button>
            </div>
        </div>
    </div>

    <div class="post-content">
        <?php if (!empty($currentPost['title'])): ?>
            <h4 class="post-title"><?= htmlspecialchars($currentPost['title']) ?></h4>
        <?php endif; ?>
        <p class="post-text"><?= nl2br(htmlspecialchars($currentPost['content'] ?? '')) ?></p>
        <?php if (!empty($currentPost['imageUrl'])): ?>
            <div class="post-image">
                <img src="<?= htmlspecialchars($currentPost['imageUrl']) ?>" alt="Post image" class="content-image" />
            </div>
        <?php endif; ?>
    </div>

    <div class="post-actions">
        <?php require __DIR__ . '/user.community-post-actions.php'; ?>
    </div>

    <?php if ($isOwner): ?>
        <form id="deleteForm-<?= $postId ?>" action="/user/community/posts/delete" method="post" style="display: none;">
            <input type="hidden" name="postId" value="<?= $postId ?>" />
        </form>

        <div id="editData-<?= $postId ?>" style="display: none;"
             data-post-id="<?= $postId ?>"
             data-content="<?= htmlspecialchars($currentPost['content'] ?? '', ENT_QUOTES) ?>"
             data-title="<?= htmlspecialchars($currentPost['title'] ?? '', ENT_QUOTES) ?>"
             data-post-type="<?= htmlspecialchars($currentPost['postType'] ?? 'general') ?>"
             data-anonymous="<?= !empty($currentPost['anonymous']) ? 'true' : 'false' ?>"
             data-image-url="<?= htmlspecialchars($currentPost['imageUrl'] ?? '', ENT_QUOTES) ?>">
        </div>
    <?php endif; ?>
</div>
