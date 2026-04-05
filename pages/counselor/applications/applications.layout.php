<!DOCTYPE html>
<html lang="en">
<?php
$pageTitle = 'Apply as a Counselor - NewPath';
$authCss = ['counselor-application.css'];
require_once __DIR__ . '/../../auth/common/auth.head.php';
?>
<body>
<div class="application-container">
    <a href="/" class="back-link">&larr; Back to Home</a>
    <div class="application-header">
        <h1>Apply as a Counselor</h1>
        <p>Join our team of professional counselors and make a difference in people's lives</p>
    </div>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-section">
            <h3 class="section-title">Personal Information</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="fullName">Full Name <span class="required">*</span></label>
                    <input type="text" id="fullName" name="fullName" required value="<?= htmlspecialchars(Request::post('fullName') ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" required value="<?= htmlspecialchars(Request::post('email') ?? '') ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="phoneNumber">Phone Number <span class="required">*</span></label>
                    <input type="tel" id="phoneNumber" name="phoneNumber" required value="<?= htmlspecialchars(Request::post('phoneNumber') ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="title">Professional Title</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars(Request::post('title') ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">Professional Information</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="specialty">Specialty <span class="required">*</span></label>
                    <select id="specialty" name="specialty" required>
                        <option value="">Select Specialty</option>
                        <?php foreach (['Addiction Counseling','Mental Health Counseling','Family Therapy','Cognitive Behavioral Therapy','Trauma Counseling','Youth Counseling','Group Therapy','Other'] as $specialty): ?>
                            <option value="<?= htmlspecialchars($specialty) ?>" <?= Request::post('specialty') === $specialty ? 'selected' : '' ?>><?= htmlspecialchars($specialty) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="experienceYears">Years of Experience</label>
                    <input type="number" id="experienceYears" name="experienceYears" min="0" max="50" value="<?= htmlspecialchars(Request::post('experienceYears') ?? '') ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="consultationFee">Consultation Fee (per session)</label>
                    <input type="number" id="consultationFee" name="consultationFee" min="0" step="0.01" value="<?= htmlspecialchars(Request::post('consultationFee') ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="languagesSpoken">Languages Spoken</label>
                    <input type="text" id="languagesSpoken" name="languagesSpoken" value="<?= htmlspecialchars(Request::post('languagesSpoken') ?? '') ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="bio">Professional Bio <span class="required">*</span></label>
                <textarea id="bio" name="bio" required><?= htmlspecialchars(Request::post('bio') ?? '') ?></textarea>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">Education & Credentials</h3>
            <div class="form-group">
                <label for="education">Education <span class="required">*</span></label>
                <textarea id="education" name="education" required><?= htmlspecialchars(Request::post('education') ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label for="certifications">Certifications & Licenses</label>
                <textarea id="certifications" name="certifications"><?= htmlspecialchars(Request::post('certifications') ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label for="documentsFile">Supporting Documents (certificates, licenses)</label>
                <input type="file" id="documentsFile" name="documentsFile" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                <small style="color:#666;font-size:0.85rem;">Accepted formats: JPG, PNG, PDF, DOC, DOCX. Max 10 MB.</small>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">Availability</h3>
            <div class="form-group">
                <label for="availabilitySchedule">Availability Schedule</label>
                <textarea id="availabilitySchedule" name="availabilitySchedule"><?= htmlspecialchars(Request::post('availabilitySchedule') ?? '') ?></textarea>
            </div>
        </div>

        <button type="submit" class="submit-btn">Submit Application</button>
    </form>
</div>
</body>
</html>
