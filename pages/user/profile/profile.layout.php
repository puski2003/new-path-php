<!DOCTYPE html>
<html lang="en">
<?php require_once __DIR__ . '/../common/user.html.head.php'; ?>
<body>
    <main class="main-container">
        <?php $activePage = 'community'; require_once __DIR__ . '/../common/user.sidebar.php'; ?>

        <section class="main-content">
            <img src="/assets/img/main-content-head.svg" alt="Main Content Head background" class="main-header-bg-image" />

            <div class="main-content-header">
                <div class="main-content-header-text">
                    <h2><?= $profile['isOwnProfile'] ? 'My Profile' : htmlspecialchars($profile['displayName']) ?>'s Profile</h2>
                    <p>Recovery Community Member</p>
                </div>
            </div>

            <div class="main-content-body">
                <div class="profile-container">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <?php if (!empty($profile['profilePicture'])): ?>
                                <img src="<?= htmlspecialchars($profile['profilePicture']) ?>" alt="<?= htmlspecialchars($profile['displayName']) ?>" />
                            <?php else: ?>
                                <img src="/assets/img/avatar.png" alt="Avatar" />
                            <?php endif; ?>
                        </div>
                        <div class="profile-info">
                            <h2 class="profile-name"><?= htmlspecialchars($profile['displayName']) ?></h2>
                            <?php if (!empty($profile['username'])): ?>
                                <span class="profile-username">@<?= htmlspecialchars($profile['username']) ?></span>
                            <?php endif; ?>
                            <div class="profile-stats">
                                <span class="stat"><strong><?= $profile['followersCount'] ?></strong> followers</span>
                                <span class="stat"><strong><?= $profile['followingCount'] ?></strong> following</span>
                                <span class="stat"><strong><?= date('M Y', strtotime($profile['createdAt'])) ?></strong> joined</span>
                            </div>
                        </div>
                        <div class="profile-actions">
                            <?php if ($profile['isOwnProfile']): ?>
                                <button class="btn btn-primary" id="editProfileBtn">
                                    <i data-lucide="edit-2" stroke-width="2"></i>
                                    Edit Profile
                                </button>
                            <?php else: ?>
                                <?php if ($profile['isFollowing']): ?>
                                    <button class="btn btn-follow following" id="followBtn" data-user-id="<?= $profile['userId'] ?>">
                                        <i data-lucide="user-check" stroke-width="2"></i>
                                        Following
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-follow" id="followBtn" data-user-id="<?= $profile['userId'] ?>">
                                        <i data-lucide="user-plus" stroke-width="2"></i>
                                        Follow
                                    </button>
                                <?php endif; ?>
                                <button class="btn btn-chat" id="messageBtn" data-user-id="<?= $profile['userId'] ?>">
                                    <i data-lucide="message-circle" stroke-width="2"></i>
                                    Message
                                </button>
                                <button class="btn btn-more" id="moreBtn">
                                    <i data-lucide="more-horizontal" stroke-width="2"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($profile['bio'])): ?>
                    <div class="profile-bio">
                        <h3>Bio</h3>
                        <p><?= nl2br(htmlspecialchars($profile['bio'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <?php if ($profile['isOwnProfile']): ?>
    <div class="edit-profile-modal" id="editProfileModal">
        <div class="edit-profile-modal-content">
            <div class="modal-header">
                <h3>Edit Profile</h3>
                <button class="modal-close" id="closeEditModal">&times;</button>
            </div>
            <form id="editProfileForm">
                <div class="form-group">
                    <label>Display Name</label>
                    <input type="text" name="display_name" class="form-input" value="<?= htmlspecialchars($profile['displayName']) ?>" />
                </div>
                <div class="form-group">
                    <label>Bio</label>
                    <textarea name="bio" class="form-input" rows="4"><?= htmlspecialchars($profile['bio']) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php require __DIR__ . '/../common/user.community-chat.php'; ?>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="/assets/js/user/profile/profile.js"></script>
    <script src="/assets/js/components/polling.js"></script>
    <script src="/assets/js/user/community/chat.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>
</body>
</html>
