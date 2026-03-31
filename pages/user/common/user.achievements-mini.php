<div class="achievements-mini">
    <h4 class="achievements-mini-title">Milestones</h4>
    <div class="achievements-badges">
        <div class="achievement-badge-item <?= $daysSober >= 7 ? 'earned' : 'locked' ?>" title="7 Days Sober">
            <div class="badge-circle">
                <?php if ($daysSober >= 7): ?>
                    <span class="badge-icon">?</span>
                <?php else: ?>
                    <span class="badge-number">7</span>
                <?php endif; ?>
            </div>
            <span class="badge-label">7 Days</span>
        </div>

        <div class="achievement-badge-item <?= $daysSober >= 30 ? 'earned' : 'locked' ?>" title="30 Days Sober">
            <div class="badge-circle">
                <?php if ($daysSober >= 30): ?>
                    <span class="badge-icon">?</span>
                <?php else: ?>
                    <span class="badge-number">30</span>
                <?php endif; ?>
            </div>
            <span class="badge-label">1 Month</span>
        </div>

        <div class="achievement-badge-item <?= $daysSober >= 90 ? 'earned' : 'locked' ?>" title="90 Days Sober">
            <div class="badge-circle">
                <?php if ($daysSober >= 90): ?>
                    <span class="badge-icon">??</span>
                <?php else: ?>
                    <span class="badge-number">90</span>
                <?php endif; ?>
            </div>
            <span class="badge-label">3 Months</span>
        </div>

        <div class="achievement-badge-item <?= $daysSober >= 180 ? 'earned' : 'locked' ?>" title="180 Days Sober">
            <div class="badge-circle">
                <?php if ($daysSober >= 180): ?>
                    <span class="badge-icon">?</span>
                <?php else: ?>
                    <span class="badge-number">180</span>
                <?php endif; ?>
            </div>
            <span class="badge-label">6 Months</span>
        </div>
    </div>
</div>
