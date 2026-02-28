<?php
$avatarUrl = !empty($counselor['profile_picture']) ? $counselor['profile_picture'] : '/assets/img/avatar.png';
$name = $counselor['name'] ?? 'Counselor';
$title = $counselor['title'] ?? 'Counselor';
$specialty = $counselor['specialty'] ?? 'Specialist';
$rating = $counselor['rating'] ?? '4.8';
$totalReviews = (int)($counselor['total_reviews'] ?? 150);
if ($totalReviews <= 0) {
    $totalReviews = 150;
}
$priceFormatted = $counselor['price_formatted'] ?? 'Rs. 0.00';
?>

<div class="counselor-profile-card">
    <div class="counselor-profile-avatar">
        <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="<?= htmlspecialchars($name) ?>" />
    </div>
    <div class="counselor-profile-info">
        <h3 class="counselor-profile-name"><?= htmlspecialchars($name) ?></h3>
        <p class="counselor-profile-title"><?= htmlspecialchars($title) ?></p>
        <p class="counselor-profile-specialty"><?= htmlspecialchars($specialty) ?></p>
        <div class="counselor-rating">
            <div class="stars">
                <img src="/assets/icons/star.svg" class="star" alt="star" />
                <img src="/assets/icons/star.svg" class="star" alt="star" />
                <img src="/assets/icons/star.svg" class="star" alt="star" />
                <img src="/assets/icons/star.svg" class="star" alt="star" />
                <img src="/assets/icons/star.svg" class="star" alt="star" />
            </div>
            <span class="rating-score"><?= htmlspecialchars((string)$rating) ?> (<?= $totalReviews ?> reviews)</span>
        </div>
    </div>
    <div class="counselor-profile-pricing">
        <span class="price-label">Per Hour</span>
        <span class="price-amount"><?= htmlspecialchars($priceFormatted) ?></span>
    </div>
</div>
