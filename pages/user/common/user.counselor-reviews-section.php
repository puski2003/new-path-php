<?php
$ratingScore = $counselor['rating'] ?? '4.8';
$reviewCount = (int)($counselor['total_reviews'] ?? 150);
if ($reviewCount <= 0) {
    $reviewCount = 150;
}
?>

<div class="reviews-section">
    <h4>Client Reviews</h4>
    <div class="reviews-overview">
        <div class="rating-summary">
            <div class="rating-score-large"><?= htmlspecialchars((string)$ratingScore) ?></div>
            <div class="rating-stars-large">
                <img src="/assets/icons/star.svg" class="star" alt="star-icon" />
                <img src="/assets/icons/star.svg" class="star" alt="star-icon" />
                <img src="/assets/icons/star.svg" class="star" alt="star-icon" />
                <img src="/assets/icons/star.svg" class="star" alt="star-icon" />
                <img src="/assets/icons/star.svg" class="star" alt="star-icon" />
            </div>
            <div class="rating-count"><?= $reviewCount ?> reviews</div>
        </div>
        <div class="rating-breakdown">
            <div class="rating-bar">
                <span>5</span>
                <div class="bar"><div class="fill" style="width: 70%"></div></div>
                <span>70%</span>
            </div>
            <div class="rating-bar">
                <span>4</span>
                <div class="bar"><div class="fill" style="width: 23%"></div></div>
                <span>23%</span>
            </div>
            <div class="rating-bar">
                <span>3</span>
                <div class="bar"><div class="fill" style="width: 5%"></div></div>
                <span>5%</span>
            </div>
            <div class="rating-bar">
                <span>2</span>
                <div class="bar"><div class="fill" style="width: 3%"></div></div>
                <span>3%</span>
            </div>
            <div class="rating-bar">
                <span>1</span>
                <div class="bar"><div class="fill" style="width: 2%"></div></div>
                <span>2%</span>
            </div>
        </div>
    </div>

    <div class="individual-reviews">
        <div class="review-item">
            <div class="review-header">
                <img src="/assets/img/avatar.png" alt="Ethan Carter" class="reviewer-avatar" />
                <div class="reviewer-info">
                    <span class="reviewer-name">Ethan Carter</span>
                    <span class="review-date">2 months ago</span>
                </div>
            </div>
            <div class="review-rating">
                <img src="/assets/icons/star.svg" class="star" alt="star-icon" />
                <img src="/assets/icons/star.svg" class="star" alt="star-icon" />
                <img src="/assets/icons/star.svg" class="star" alt="star-icon" />
                <img src="/assets/icons/star.svg" class="star" alt="star-icon" />
                <img src="/assets/icons/star.svg" class="star" alt="star-icon" />
            </div>
            <p class="review-text">Dr Harper's guidance has been instrumental in my recovery. Her compassionate approach and expertise helped me navigate challenging times and build a strong foundation for my sobriety.</p>
        </div>

        <div class="review-item">
            <div class="review-header">
                <img src="/assets/img/avatar.png" alt="Liam Walker" class="reviewer-avatar" />
                <div class="reviewer-info">
                    <span class="reviewer-name">Liam Walker</span>
                    <span class="review-date">4 months ago</span>
                </div>
            </div>
            <div class="review-rating">
                <img src="/assets/icons/star.svg" class="star" alt="star-icon" />
                <img src="/assets/icons/star.svg" class="star" alt="star-icon" />
                <img src="/assets/icons/star.svg" class="star" alt="star-icon" />
                <img src="/assets/icons/star.svg" class="star" alt="star-icon" />
                <img src="/assets/icons/star.svg" class="star" alt="star-icon" />
            </div>
            <p class="review-text">Dr Harper is an exceptional counsellor. Her support and understanding made me feel comfortable and empowered to take control of my recovery journey.</p>
        </div>
    </div>
</div>
