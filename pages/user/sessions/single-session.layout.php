<!DOCTYPE html>
<html lang="en">
<?php require_once __DIR__ . '/../common/user.html.head.php'; ?>

<body>
    <main class="main-container">
        <?php $activePage = 'sessions';
        require_once __DIR__ . '/../common/user.sidebar.php'; ?>

        <section class="main-content">
            <img src="/assets/img/main-content-head.svg"
                alt="Main Content Head background"
                class="main-header-bg-image" />

            <div class="main-content-header">
                <div class="main-content-header-text">
                    <h2>My Sessions</h2>
                    <p>Your scheduled guidance, all in one place.</p>
                </div>

                <div style="width: 25%"></div>
                <img src="/assets/img/session-header.svg"
                    alt="Session"
                    class="session-image" />
            </div>

            <div class="main-content-body">
                <div class="session-detail-header">
                    <a class="back-btn" href="/user/sessions" aria-label="Back to sessions">
                        <i data-lucide="arrow-left" class="back-icon" stroke-width="1.8"></i>
                    </a>
                </div>

                <div class="session-detail-card">
                    <div class="session-detail-info">
                        <div class="doctor-avatar">
                            <img src="<?= htmlspecialchars($sessionData['profilePicture']) ?>" alt="<?= htmlspecialchars($sessionData['doctorName']) ?>" />
                        </div>
                        <div class="doctor-details">
                            <h2 class="doctor-name"><?= htmlspecialchars($sessionData['doctorName']) ?></h2>
                            <p class="doctor-title"><?= htmlspecialchars($sessionData['doctorTitle']) ?></p>
                            <p class="doctor-specialization"><?= htmlspecialchars($sessionData['specialization']) ?></p>
                        </div>
                        <div class="session-detail-actions">
                            <?php if ($isUpcomingSession): ?>
                                <?php if (!empty($sessionData['meetingLink'])): ?>
                                    <a class="btn btn-primary session-join-btn" href="<?= htmlspecialchars($sessionData['meetingLink']) ?>" target="_blank" rel="noopener">Join session</a>
                                <?php else: ?>
                                    <button class="btn btn-primary session-join-btn" type="button">Join session</button>
                                <?php endif; ?>
                                <a class="btn btn-secondary session-reschedule-btn" href="/user/counselors?id=<?= (int)$sessionData['counselorId'] ?>">Reschedule</a>
                            <?php else: ?>
                                <a class="btn btn-primary session-join-btn" href="/user/counselors?id=<?= (int)$sessionData['counselorId'] ?>">Rebook</a>
                                <button class="btn btn-secondary session-reschedule-btn" type="button">Review</button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="session-info-grid">
                        <div class="session-info-item">
                            <span class="session-info-label">Session type</span>
                            <span class="session-info-value"><?= htmlspecialchars($sessionData['sessionType']) ?></span>
                        </div>
                        <div class="session-info-item">
                            <span class="session-info-label">Location</span>
                            <span class="session-info-value"><?= htmlspecialchars($sessionData['location']) ?></span>
                        </div>
                        <div class="session-info-item">
                            <span class="session-info-label">Booking ID</span>
                            <span class="session-info-value" id="booking-id" style="cursor: pointer;"><?= htmlspecialchars($sessionData['bookingId']) ?> (Copy)</span>
                        </div>
                    </div>

                    <div class="session-timeline-section">
                        <h3 class="timeline-title">Timeline</h3>
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div class="timeline-content">
                                    <h4 class="timeline-event">Booked at</h4>
                                    <p class="timeline-time"><?= htmlspecialchars($sessionData['bookedAt']) ?></p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div class="timeline-content">
                                    <h4 class="timeline-event">Payment captured</h4>
                                    <p class="timeline-time"><?= htmlspecialchars($sessionData['paymentCaptured']) ?></p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div class="timeline-content">
                                    <h4 class="timeline-event">Join window opens</h4>
                                    <p class="timeline-time"><?= htmlspecialchars($sessionData['joinWindow']) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="session-notes-section">
                        <h3 class="notes-title">Notes</h3>
                        <p class="notes-content"><?= htmlspecialchars($sessionData['notes']) ?></p>
                    </div>

                    <div class="session-payment-section">
                        <div class="payment-header">
                            <h3 class="payment-title">Payment</h3>
                            <button class="btn btn-bg-light-green view-order-btn" type="button">View order</button>
                        </div>
                        <div class="payment-details">
                            <div class="payment-method">
                                <img src="/assets/img/visa.png" alt="Visa" class="payment-icon" />
                                <span class="payment-card"><?= htmlspecialchars($sessionData['cardNumber']) ?></span>
                                <span class="payment-expiry"><?= htmlspecialchars($sessionData['cardExpiry']) ?></span>
                            </div>
                            <button class="btn btn-bg-light-green download-receipt-btn" type="button">Download receipt (PDF)</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="/assets/js/user/sessions-view-more.js"></script>
    <script src="/assets/js/auth/user-profile.js"></script>
    <script>
        if (typeof lucide !== 'undefined') lucide.createIcons();

        document.addEventListener('DOMContentLoaded', function() {
            var bookingId = document.getElementById('booking-id');
            if (!bookingId) return;
            bookingId.addEventListener('click', function() {
                var text = bookingId.textContent.replace(' (Copy)', '').trim();
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text);
                }
            });
        });
    </script>
</body>

</html>

