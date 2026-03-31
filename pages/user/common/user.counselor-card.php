<?php

/**
 * Counselor Card Component
 * Expects $counselor array to be available.
 */
$avatarUrl = !empty($counselor['profile_picture']) ? $counselor['profile_picture'] : '/assets/img/avatar.png';
$name = $counselor['name'] ?? 'Counselor';
$specialty = $counselor['specialty_short'] ?? 'Specialist';
$experience = ($counselor['experience_years'] ?? '0') . ' years experience';
$rating = number_format((float)($counselor['rating_value'] ?? 0), 1);
$price = 'Rs. ' . number_format((float)($counselor['consultation_fee'] ?? 0), 2);
$counselorId = $counselor['counselor_id'] ?? 0;
?>

<div class="counselor-card">
    <div class="counselor-avatar">
        <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="<?= htmlspecialchars($name) ?>" />
    </div>
    <div class="counselor-info">
        <span class="counselor-specialty"><?= htmlspecialchars($specialty) ?></span>
        <h3 class="counselor-name"><?= htmlspecialchars($name) ?></h3>
        <p class="counselor-schedule"><?= htmlspecialchars($experience) ?></p>
        <div class="counselor-rating">
            <div class="stars">
                <img src="/assets/icons/star.svg" class="star" alt="star-icon" />
                <img src="/assets/icons/star.svg" class="star" alt="star-icon" />
                <img src="/assets/icons/star.svg" class="star" alt="star-icon" />
                <img src="/assets/icons/star.svg" class="star" alt="star-icon" />
                <img src="/assets/icons/star.svg" class="star" alt="star-icon" />
            </div>
            <span class="rating-score"><?= $rating ?></span>
        </div>
    </div>
    <div class="counselor-actions">
        <div class="counselor-price">
            <span class="price-label">per hour</span>
            <span class="price-amount"><?= $price ?></span>
        </div>
        <a href="/user/counselors?id=<?= $counselorId ?>" class="btn btn-secondary">View Profile</a>
    </div>
</div>