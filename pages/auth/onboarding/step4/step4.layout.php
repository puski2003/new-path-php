<?php
$pageTitle = 'NewPath - Select Your Plan';
$authCss   = 'onboarding.css';
require_once __DIR__ . '/../../common/auth.head.php';

// Risk band display config
$bandConfig = [
    'LOW'      => ['label' => 'Low Risk',      'color' => '#2e7d32', 'bg' => '#e8f5e9', 'subtitle' => 'A self-guided plan is a great starting point for you.'],
    'MODERATE' => ['label' => 'Moderate Risk',  'color' => '#e65100', 'bg' => '#fff3e0', 'subtitle' => 'Both options suit you well — choose what feels right.'],
    'HIGH'     => ['label' => 'High Risk',      'color' => '#c62828', 'bg' => '#ffebee', 'subtitle' => 'We recommend starting with a counselor for personalised support.'],
];
$band = $bandConfig[$riskBand];
?>

<body class="theme-user">
    <div class="onboarding-container">
        <header>
            <div class="logo-container" style="padding: 10px;">
                <img src="/assets/img/logo.svg" alt="NewPath Logo" class="logo">
                <span class="logo-text">New<br>Path</span>
            </div>
        </header>

        <div class="onboarding-content">
            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress-text">Step 4 of 5</div>
                    <div class="progress-track" aria-label="Progress">
                        <div class="progress-fill" style="width: 80%;"></div>
                    </div>
                </div>
            </div>

            <div class="onboarding-form-container">
                <h1 class="onboarding-title">Choose Your Recovery Path</h1>

                <!-- Risk band banner -->
                <div style="
                    display: flex; align-items: center; gap: 12px;
                    background: <?= $band['bg'] ?>; border-radius: 8px;
                    padding: 12px 18px; margin-bottom: 24px; flex-wrap: wrap;
                ">
                    <span style="
                        background: <?= $band['color'] ?>; color: #fff;
                        font-size: 0.75rem; font-weight: 700; padding: 3px 10px;
                        border-radius: 20px; white-space: nowrap; letter-spacing: 0.5px;
                    "><?= $band['label'] ?></span>
                    <span style="color: <?= $band['color'] ?>; font-size: 0.9rem;">
                        <?= $band['subtitle'] ?>
                    </span>
                </div>

                <?php if ($error !== null): ?>
                    <div class="error-message" style="color: red; margin-bottom: 15px; padding: 10px; border: 1px solid red; background-color: #ffe6e6;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form class="onboarding-form" id="recoveryPlanForm" method="POST" action="/auth/onboarding/step4">
                    <div class="plan-selection-container">

                        <!-- System Plan -->
                        <div class="plan-card <?= $defaultPlan === 'system' ? 'selected' : '' ?>"
                             style="position: relative;"
                             onclick="selectPlan('system', this)">

                            <?php if ($riskBand === 'LOW'): ?>
                                <span style="
                                    position: absolute; top: 12px; right: 12px;
                                    background: #2e7d32; color: #fff;
                                    font-size: 0.7rem; font-weight: 700;
                                    padding: 2px 8px; border-radius: 20px;
                                ">Recommended</span>
                            <?php endif; ?>

                            <div class="plan-icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                                </svg>
                            </div>
                            <h3 class="plan-title">System Plan</h3>
                            <p class="plan-description">A structured, self-guided journey using our proven tools and resources.</p>
                            <ul class="plan-features">
                                <li>Daily check-ins &amp; tracking</li>
                                <li>Guided exercises &amp; journaling</li>
                                <li>Community support access</li>
                                <li>Free forever</li>
                            </ul>
                        </div>

                        <!-- Counselor Plan -->
                        <div class="plan-card <?= $defaultPlan === 'counselor' ? 'selected' : '' ?>"
                             style="position: relative;"
                             onclick="selectPlan('counselor', this)">

                            <?php if ($riskBand === 'HIGH'): ?>
                                <span style="
                                    position: absolute; top: 12px; right: 12px;
                                    background: #c62828; color: #fff;
                                    font-size: 0.7rem; font-weight: 700;
                                    padding: 2px 8px; border-radius: 20px;
                                ">Recommended</span>
                            <?php endif; ?>

                            <div class="plan-icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                            </div>
                            <h3 class="plan-title">Counselor Plan</h3>
                            <p class="plan-description">Work directly with a certified professional tailored to your specific needs.</p>
                            <ul class="plan-features">
                                <li>Everything in System Plan</li>
                                <li>1-on-1 video sessions</li>
                                <li>Personalised recovery strategy</li>
                                <li>Direct messaging support</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Disclaimer -->
                    <p style="
                        text-align: center; font-size: 0.8rem;
                        color: var(--color-text-muted, #888); margin: 20px 0 8px;
                    ">
                        This recommendation is based on your answers and is not a medical diagnosis.
                        You are free to choose either option.
                    </p>

                    <input type="hidden" name="selectedPlan" id="selectedPlan" value="<?= htmlspecialchars($defaultPlan) ?>" required>

                    <div class="form-actions-recovery">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='/auth/onboarding/step3'">Back</button>
                        <button type="submit" class="form-submit-btn">Continue to Dashboard</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function selectPlan(planType, cardElement) {
            document.getElementById('selectedPlan').value = planType;
            document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
            cardElement.classList.add('selected');
        }
    </script>
</body>

</html>
