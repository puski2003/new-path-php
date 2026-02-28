<?php

/**
 * User Dashboard Layout
 * Variables available: $user, $data (from controller)
 */
$activePage = 'dashboard';



?>
<!DOCTYPE html>
<html lang="en">

<?php
$pageTitle = "User Dashboard — New Path";
$pageStyle = ["user/dashboard"];
require_once __DIR__ . '/../common/user.html.head.php';
?>

<body>
    <main class="main-container">
        <?php require_once __DIR__ . '/../common/user.sidebar.php'; ?>

        <section class="main-content">
            <img
                src="/assets/img/main-content-head.svg"
                alt="Main Content Head background"
                class="main-header-bg-image" />

            <div class="main-content-header">
                <div class="main-content-header-text">
                    <h2>Hi, <?= htmlspecialchars(explode(' ', $userName)[0]) ?>! 👋</h2>
                    <p>Track your progress and stay motivated</p>
                </div>

                <div class="card-container">
                    <div class="card days-sober-card">
                        <div class="days-sober-content">
                            <p>DAYS SOBER</p>
                            <i data-lucide="heart" stroke-width="1" style="color: #335346"></i>
                        </div>
                        <h2><?= $daysSober ?></h2>
                    </div>

                    <div class="card milestone-progress-card">
                        <p>SOBRIETY PROGRESS</p>
                        <span><?= $milestoneProgress ?>%</span>
                        <div class="progress" style="--value: <?= $milestoneProgress ?>%">
                            <div class="bar"></div>
                            <div class="thumb" aria-label="Progress <?= $milestoneProgress ?> percent"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="main-content-body">
                <div class="inner-body-content">
                    <div class="body-column">
                        <!-- Due Now Section -->
                        <div class="col-1-row-1 dashboard-card">
                            <div class="card-header">
                                <h3>Due now</h3>
                            </div>
                            <div class="due-now-content">
                                <?php if (!empty($nextSession)): ?>
                                    <div class="upcoming-session">
                                        <div class="upcoming-session-info">
                                            <i
                                                data-lucide="calendar"
                                                stroke-width="1"
                                                class="session-icon"></i>
                                            <div class="session-details">
                                                <h4>Upcoming Session</h4>
                                                <p>With <?= htmlspecialchars($nextSession['counselorName']) ?>, <?= htmlspecialchars($nextSession['formattedTime']) ?></p>
                                            </div>
                                        </div>
                                        <a href="<?= htmlspecialchars($nextSession['meetingLink']) ?>" class="btn btn-primary btn-sm" target="_blank">Join</a>
                                    </div>
                                <?php else: ?>
                                    <div class="upcoming-session">
                                        <div class="upcoming-session-info">
                                            <i
                                                data-lucide="calendar"
                                                stroke-width="1"
                                                class="session-icon"></i>
                                            <div class="session-details">
                                                <h4>No Upcoming Sessions</h4>
                                                <p>Book a session with a counselor</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Community Highlights Section -->
                        <div class="col-1-row-2 dashboard-card">
                            <div class="card-header">
                                <h3>Community highlights</h3>
                                <i data-lucide="shield" stroke-width="1"></i>
                            </div>
                            <div class="community-highlights">
                                <?php if (!empty($communityHighlights)): ?>
                                    <?php foreach ($communityHighlights as $post): ?>
                                        <div class="highlight-item">
                                            <img
                                                src="<?= !empty($post['profilePictureUrl']) ? htmlspecialchars($post['profilePictureUrl']) : '/assets/img/avatar.png' ?>"
                                                alt="<?= htmlspecialchars($post['displayName']) ?>"
                                                class="user-avatar" />
                                            <div class="highlight-info">
                                                <span class="username"><?= $post['anonymous'] ? 'Anonymous' : htmlspecialchars($post['displayName']) ?></span>
                                                <span class="time"><?= htmlspecialchars($post['title']) ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="highlight-item">
                                        <div class="highlight-info">
                                            <span class="username" style="color: var(--color-text-muted)">No community posts yet</span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="body-column">
                        <!-- Daily Tasks Section -->
                        <div class="col-2-row-1 dashboard-card">
                            <div class="card-header">
                                <h3>Daily Tasks</h3>
                                <a href="/user/daily-tasks" class="view-all-link">
                                    View All
                                    <i data-lucide="arrow-right" stroke-width="2"></i>
                                </a>
                            </div>
                            <div class="daily-tasks">
                                <?php if (!empty($dailyTasks)): ?>
                                    <?php foreach ($dailyTasks as $task): ?>
                                        <div class="task-item" data-task-id="<?= $task['id'] ?>">
                                            <div class="task-checkbox <?= $task['completed'] ? 'completed' : '' ?> <?= $task['urgent'] ? 'urgent' : '' ?>"></div>
                                            <span class="task-text <?= $task['completed'] ? 'completed' : '' ?>"><?= htmlspecialchars($task['title']) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="task-item">
                                        <span class="task-text" style="color: var(--color-text-muted)">No tasks for today</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Progress Tracker Section -->
                        <a href="/user/progress-tracker"></a>
                        <div class="col-2-row-2 dashboard-card progress-tracker-card">
                            <div class="card-header">
                                <h3>Plan Tracker</h3>
                                <i data-lucide="arrow-right" stroke-width="1" class="arrow-icon"></i>
                            </div>
                            <div class="progress-content">
                                <div class="progress-bar-wrapper">
                                    <div class="progress-bar-container">
                                        <div
                                            class="progress-bar-fill"
                                            style="--progress: <?= $progressPercentage ?>%"></div>
                                    </div>
                                    <span class="progress-percentage"><?= $progressPercentage ?>%</span>
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>

                    <div class="body-column">
                        <!-- Quick Log Section -->
                        <div class="col-3-row-1 dashboard-card">
                            <div class="card-header">
                                <h3>Quick Log</h3>
                                <?php if (isset($_GET['logSuccess']) && $_GET['logSuccess'] === 'true'): ?>
                                    <span style="color: var(--color-success); font-size: 0.875rem;">✓ Saved!</span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <form action="/user/quick-log" method="POST" class="quick-log-content">
                                    <textarea
                                        class="log-textarea"
                                        name="logText"
                                        placeholder="How are you feeling today? Tell us about your mood, any urges, or what's on your mind..."
                                        required></textarea>
                                    <button type="submit" class="btn btn-primary log-submit-btn">
                                        Save Log
                                    </button>
                                </form>
                                <?php if (isset($_GET['error']) && $_GET['error'] === 'empty'): ?>
                                    <p style="color: var(--color-error); font-size: 0.75rem; margin-top: 0.5rem;">Please enter something before saving.</p>
                                <?php endif; ?>
                                <?php if (isset($_GET['error']) && $_GET['error'] === 'saveFailed'): ?>
                                    <p style="color: var(--color-error); font-size: 0.75rem; margin-top: 0.5rem;">Could not save log. Please try again.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Achievements Section -->
                        <div class="col-3-row-2 dashboard-card achievements-card">
                            <div class="card-header">
                                <h3>Achievements</h3>
                                <div class="achievement-icon">
                                    <i data-lucide="trophy" stroke-width="1"></i>
                                </div>
                            </div>
                            <div class="achievement-content">
                                <?php if ($achievements['oneYear']): ?>
                                    <div class="achievement-badge">
                                        <i data-lucide="crown" stroke-width="1"></i>
                                        <span class="achievement-text">1 Year Champion! 🎉</span>
                                    </div>
                                <?php elseif ($achievements['sixMonths']): ?>
                                    <div class="achievement-badge">
                                        <i data-lucide="medal" stroke-width="1"></i>
                                        <span class="achievement-text">6 Months Strong! 💪</span>
                                    </div>
                                <?php elseif ($achievements['threeMonths']): ?>
                                    <div class="achievement-badge">
                                        <i data-lucide="award" stroke-width="1"></i>
                                        <span class="achievement-text">90 Days Strong!</span>
                                    </div>
                                <?php elseif ($achievements['firstMonth']): ?>
                                    <div class="achievement-badge">
                                        <i data-lucide="star" stroke-width="1"></i>
                                        <span class="achievement-text">First Month Complete!</span>
                                    </div>
                                <?php elseif ($achievements['sevenDays']): ?>
                                    <div class="achievement-badge">
                                        <i data-lucide="circle-star" stroke-width="1"></i>
                                        <span class="achievement-text">7 Days Strong</span>
                                    </div>
                                <?php else: ?>
                                    <div class="achievement-badge">
                                        <i data-lucide="target" stroke-width="1"></i>
                                        <span class="achievement-text">First week goal: <?= $nextMilestone ?> days</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Motivational Quote Section -->
                        <div class="col-3-row-3 dashboard-card quote-card">
                            <div class="quote-content">
                                <i
                                    data-lucide="quote"
                                    stroke-width="2"
                                    class="quote-icon"
                                    style="color: #335345; transform: scale(-1, -1)"></i>
                                <p
                                    class="quote-text"
                                    style="color: #335345; font-weight: 600">
                                    "You are stronger than you think."
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
    <script src="/assets/js/user/dashboard.js"></script>
    <script src="/assets/js/components/sidebar.js"></script>
    <script src="/assets/js/auth/user-profile.js"></script>
</body>

</html>