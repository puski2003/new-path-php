<?php

class AdminData
{
    private static function esc(string $value): string
    {
        Database::setUpConnection();
        return Database::$connection->real_escape_string($value);
    }

    private static function roleLabel(string $role): string
    {
        return match ($role) {
            'user' => 'Recovering User',
            'counselor' => 'Counselor',
            'admin' => 'Admin',
            default => ucfirst($role),
        };
    }

    public static function getAdminRecordByUserId(int $userId): ?array
    {
        $safeUserId = max(0, $userId);
        $rs = Database::search("SELECT * FROM admin WHERE user_id = $safeUserId LIMIT 1");
        return $rs ? ($rs->fetch_assoc() ?: null) : null;
    }

    public static function getAdminIdByUserId(int $userId): int
    {
        $admin = self::getAdminRecordByUserId($userId);
        return (int) ($admin['admin_id'] ?? 0);
    }

    public static function getUsers(array $filters = []): array
    {
        $where = ["role IN ('user', 'counselor', 'admin')"];

        $role = trim((string) ($filters['role'] ?? 'all'));
        if ($role !== '' && $role !== 'all') {
            $roleMap = [
                'Recovering User' => 'user',
                'Counselor' => 'counselor',
                'Admin' => 'admin',
                'Moderator' => 'admin',
            ];
            if (isset($roleMap[$role])) {
                $where[] = "role = '" . self::esc($roleMap[$role]) . "'";
            }
        }

        $status = trim((string) ($filters['status'] ?? 'all'));
        if ($status !== '' && $status !== 'all') {
            $where[] = match (strtolower($status)) {
                'active' => "is_active = 1",
                'inactive' => "is_active = 0",
                'pending' => "onboarding_completed = 0",
                default => "1=1",
            };
        }

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $safeSearch = self::esc($search);
            $where[] = "(COALESCE(display_name, CONCAT(first_name, ' ', last_name), username, email) LIKE '%$safeSearch%' OR email LIKE '%$safeSearch%')";
        }

        $dateJoined = trim((string) ($filters['dateJoined'] ?? ''));
        if ($dateJoined !== '') {
            $safeDate = self::esc($dateJoined);
            $where[] = "DATE(created_at) = '$safeDate'";
        }

        $sql = "SELECT user_id, email, role, display_name, first_name, last_name, last_login, created_at, is_active, onboarding_completed
                FROM users
                WHERE " . implode(' AND ', $where) . "
                ORDER BY created_at DESC, user_id DESC";
        $rs = Database::search($sql);

        $users = [];
        while ($rs && ($row = $rs->fetch_assoc())) {
            $name = $row['display_name']
                ?: trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''))
                ?: ($row['email'] ?? 'User');

            $engagementScore = 0;
            if (!empty($row['last_login'])) {
                $daysAgo = max(0, (int) floor((time() - strtotime($row['last_login'])) / 86400));
                $engagementScore = $daysAgo <= 3 ? 3 : ($daysAgo <= 10 ? 2 : 1);
            }
            $engagement = $engagementScore >= 3 ? 'High' : ($engagementScore === 2 ? 'Medium' : 'Low');

            $users[] = [
                'userId' => (int) $row['user_id'],
                'fullName' => $name,
                'email' => $row['email'] ?? '',
                'role' => self::roleLabel((string) ($row['role'] ?? 'user')),
                'status' => !$row['is_active'] ? 'Inactive' : (!empty($row['onboarding_completed']) ? 'Active' : 'Pending'),
                'engagement' => $engagement,
                'lastActive' => !empty($row['last_login']) ? date('M j, Y', strtotime($row['last_login'])) : 'Never',
                'registration' => !empty($row['created_at']) ? date('M j, Y', strtotime($row['created_at'])) : '-',
            ];
        }

        $engagementFilter = trim((string) ($filters['engagement'] ?? 'all'));
        if ($engagementFilter !== '' && $engagementFilter !== 'all') {
            $users = array_values(array_filter(
                $users,
                static fn(array $user): bool => strcasecmp($user['engagement'], $engagementFilter) === 0
            ));
        }

        return $users;
    }

    public static function getCounselorStats(): array
    {
        $activeCounselors = Database::search("SELECT COUNT(*) AS total FROM users WHERE role = 'counselor' AND is_active = 1");
        $pendingApplications = Database::search("SELECT COUNT(*) AS total FROM counselor_applications WHERE status = 'pending'");
        $rating = Database::search("SELECT COALESCE(AVG(rating), 0) AS avg_rating FROM counselors");
        $sessions = Database::search("SELECT COUNT(*) AS total FROM sessions");

        return [
            'activeCounselorsCount' => (int) (($activeCounselors ? $activeCounselors->fetch_assoc()['total'] : 0) ?? 0),
            'pendingApplicationsCount' => (int) (($pendingApplications ? $pendingApplications->fetch_assoc()['total'] : 0) ?? 0),
            'averageRating' => round((float) (($rating ? $rating->fetch_assoc()['avg_rating'] : 0) ?? 0), 1),
            'totalSessions' => (int) (($sessions ? $sessions->fetch_assoc()['total'] : 0) ?? 0),
        ];
    }

    public static function getCounselorApplications(string $status = 'pending'): array
    {
        $where = '';
        if ($status !== 'all') {
            $where = "WHERE ca.status = '" . self::esc($status) . "'";
        }

        $rs = Database::search(
            "SELECT ca.*, DATE(ca.created_at) AS application_date, a.full_name AS reviewer_name
             FROM counselor_applications ca
             LEFT JOIN admin a ON a.admin_id = ca.reviewed_by
             $where
             ORDER BY ca.created_at DESC"
        );

        $applications = [];
        while ($rs && ($row = $rs->fetch_assoc())) {
            $applications[] = [
                'applicationId' => (int) $row['application_id'],
                'fullName' => $row['full_name'] ?? '',
                'email' => $row['email'] ?? '',
                'phoneNumber' => $row['phone_number'] ?? '',
                'title' => $row['title'] ?? '',
                'specialty' => $row['specialty'] ?? '',
                'bio' => $row['bio'] ?? '',
                'experienceYears' => $row['experience_years'] !== null ? (int) $row['experience_years'] : null,
                'education' => $row['education'] ?? '',
                'certifications' => $row['certifications'] ?? '',
                'languagesSpoken' => $row['languages_spoken'] ?? '',
                'consultationFee' => $row['consultation_fee'] !== null ? (float) $row['consultation_fee'] : null,
                'availabilitySchedule' => $row['availability_schedule'] ?? '',
                'documentsUrl' => $row['documents_url'] ?? '',
                'status' => $row['status'] ?? 'pending',
                'adminNotes' => $row['admin_notes'] ?? '',
                'reviewerName' => $row['reviewer_name'] ?? '',
                'applicationDate' => $row['application_date'] ?? '',
                'reviewDate' => $row['review_date'] ?? '',
            ];
        }

        return $applications;
    }

    public static function getCounselorApplicationById(int $applicationId): ?array
    {
        foreach (self::getCounselorApplications('all') as $application) {
            if ($application['applicationId'] === $applicationId) {
                return $application;
            }
        }
        return null;
    }

    public static function approveCounselorApplication(int $applicationId, int $adminUserId): array
    {
        $application = self::getCounselorApplicationById($applicationId);
        if (!$application) {
            return ['ok' => false, 'message' => 'Application not found.'];
        }

        $safeEmail = self::esc($application['email']);
        $existingUser = Database::search("SELECT user_id FROM users WHERE email = '$safeEmail' LIMIT 1");
        if ($existingUser && $existingUser->num_rows > 0) {
            $row = $existingUser->fetch_assoc();
            $userId = (int) $row['user_id'];
        } else {
            $usernameBase = strtolower(preg_replace('/[^a-z0-9]+/i', '_', $application['fullName'])) ?: 'counselor';
            $username = trim($usernameBase, '_');
            if ($username === '') {
                $username = 'counselor';
            }
            $safeUsername = self::esc($username);
            $dup = Database::search("SELECT COUNT(*) AS total FROM users WHERE username LIKE '$safeUsername%'");
            $dupCount = (int) (($dup ? $dup->fetch_assoc()['total'] : 0) ?? 0);
            if ($dupCount > 0) {
                $safeUsername .= '_' . ($dupCount + 1);
            }

            $passwordHash = self::esc(password_hash(bin2hex(random_bytes(6)), PASSWORD_BCRYPT));
            $displayName = self::esc($application['fullName']);
            Database::iud(
                "INSERT INTO users (email, username, password_hash, salt, role, display_name, phone_number, is_active, created_at, updated_at)
                 VALUES ('$safeEmail', '$safeUsername', '$passwordHash', '', 'counselor', '$displayName', '" . self::esc($application['phoneNumber']) . "', 1, NOW(), NOW())"
            );
            $userId = (int) Database::$connection->insert_id;
        }

        $checkCounselor = Database::search("SELECT counselor_id FROM counselors WHERE user_id = $userId LIMIT 1");
        if (!($checkCounselor && $checkCounselor->num_rows > 0)) {
            $availability = self::esc($application['availabilitySchedule']);
            $consultationFee = $application['consultationFee'] !== null ? (float) $application['consultationFee'] : 'NULL';
            Database::iud(
                "INSERT INTO counselors
                    (user_id, title, specialty, specialty_short, bio, experience_years, education, certifications, languages_spoken, consultation_fee, availability_schedule, is_verified, created_at, updated_at)
                 VALUES
                    ($userId, '" . self::esc($application['title']) . "', '" . self::esc($application['specialty']) . "', '', '" . self::esc($application['bio']) . "', " . (int) ($application['experienceYears'] ?? 0) . ", '" . self::esc($application['education']) . "', '" . self::esc($application['certifications']) . "', '" . self::esc($application['languagesSpoken']) . "', $consultationFee, '$availability', 1, NOW(), NOW())"
            );
        }

        $adminId = self::getAdminIdByUserId($adminUserId);
        $notes = self::esc('Application approved and counselor account created');
        Database::iud(
            "UPDATE counselor_applications
             SET status = 'approved',
                 admin_notes = '$notes',
                 reviewed_by = " . max(0, $adminId) . ",
                 review_date = NOW(),
                 updated_at = NOW()
             WHERE application_id = $applicationId"
        );

        return ['ok' => true, 'message' => 'Application approved and counselor account created successfully.'];
    }

    public static function rejectCounselorApplication(int $applicationId, int $adminUserId, string $notes): array
    {
        $adminId = self::getAdminIdByUserId($adminUserId);
        $safeNotes = self::esc($notes);
        Database::iud(
            "UPDATE counselor_applications
             SET status = 'rejected',
                 admin_notes = '$safeNotes',
                 reviewed_by = " . max(0, $adminId) . ",
                 review_date = NOW(),
                 updated_at = NOW()
             WHERE application_id = $applicationId"
        );

        return ['ok' => true, 'message' => 'Application rejected successfully.'];
    }

    public static function getCounselors(array $filters = []): array
    {
        $where = ["u.role = 'counselor'"];

        $specialization = trim((string) ($filters['specialization'] ?? 'all'));
        if ($specialization !== '' && $specialization !== 'all') {
            $where[] = "c.specialty = '" . self::esc($specialization) . "'";
        }

        $status = trim((string) ($filters['counselorStatus'] ?? 'all'));
        if ($status === 'active') {
            $where[] = "u.is_active = 1";
        } elseif ($status === 'inactive') {
            $where[] = "u.is_active = 0";
        }

        $rs = Database::search(
            "SELECT c.counselor_id, c.title, c.specialty, c.languages_spoken, c.rating, c.total_reviews,
                    u.email, u.display_name, u.first_name, u.last_name, u.is_active
             FROM counselors c
             INNER JOIN users u ON u.user_id = c.user_id
             WHERE " . implode(' AND ', $where) . "
             ORDER BY c.created_at DESC, c.counselor_id DESC"
        );

        $counselors = [];
        while ($rs && ($row = $rs->fetch_assoc())) {
            $name = $row['display_name']
                ?: trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''))
                ?: ($row['email'] ?? 'Counselor');

            $counselors[] = [
                'counselorId' => (int) $row['counselor_id'],
                'fullName' => $name,
                'email' => $row['email'] ?? '',
                'title' => $row['title'] ?? '',
                'specialty' => $row['specialty'] ?? '',
                'languagesSpoken' => $row['languages_spoken'] ?? '',
                'rating' => (float) ($row['rating'] ?? 0),
                'totalReviews' => (int) ($row['total_reviews'] ?? 0),
                'active' => !empty($row['is_active']),
            ];
        }

        return $counselors;
    }

    private static function normalizeJobType(string $jobType): string
    {
        $normalized = strtolower(trim($jobType));
        return match ($normalized) {
            'full-time', 'full_time' => 'full_time',
            'part-time', 'part_time' => 'part_time',
            'contract' => 'contract',
            'temporary' => 'temporary',
            'internship' => 'internship',
            default => 'full_time',
        };
    }

    public static function getJobPosts(array $filters = []): array
    {
        $where = ['1=1'];
        $status = trim((string) ($filters['status'] ?? 'all'));
        if ($status === 'active' || $status === 'approved') {
            $where[] = 'jp.is_active = 1';
        } elseif ($status === 'inactive' || $status === 'rejected') {
            $where[] = 'jp.is_active = 0';
        }

        $location = trim((string) ($filters['location'] ?? 'all'));
        if ($location !== '' && $location !== 'all') {
            $where[] = "jp.location = '" . self::esc($location) . "'";
        }

        $jobType = trim((string) ($filters['jobType'] ?? 'all'));
        if ($jobType !== '' && $jobType !== 'all') {
            $where[] = "jp.job_type = '" . self::normalizeJobType($jobType) . "'";
        }

        $rs = Database::search(
            "SELECT jp.*, a.full_name AS admin_name
             FROM job_posts jp
             LEFT JOIN admin a ON a.admin_id = jp.created_by
             WHERE " . implode(' AND ', $where) . "
             ORDER BY jp.created_at DESC, jp.job_id DESC"
        );

        $items = [];
        while ($rs && ($row = $rs->fetch_assoc())) {
            $items[] = [
                'jobId' => (int) $row['job_id'],
                'title' => $row['title'] ?? '',
                'company' => $row['company'] ?? '',
                'description' => $row['description'] ?? '',
                'requirements' => $row['requirements'] ?? '',
                'location' => $row['location'] ?? '',
                'jobType' => ucwords(str_replace('_', '-', (string) ($row['job_type'] ?? ''))),
                'category' => $row['category'] ?? '',
                'salary' => $row['salary'] !== null ? (float) $row['salary'] : null,
                'salaryRange' => $row['salary_range'] ?? '',
                'contactEmail' => $row['contact_email'] ?? '',
                'contactPhone' => $row['contact_phone'] ?? '',
                'applicationUrl' => $row['application_url'] ?? '',
                'active' => !empty($row['is_active']),
                'createdBy' => $row['admin_name'] ?? 'Admin',
            ];
        }

        return $items;
    }

    public static function getJobPostById(int $jobId): ?array
    {
        foreach (self::getJobPosts() as $job) {
            if ($job['jobId'] === $jobId) {
                return $job;
            }
        }
        return null;
    }

    public static function createJobPost(array $input, int $adminUserId): bool
    {
        $adminId = self::getAdminIdByUserId($adminUserId);
        $salary = is_numeric($input['salary'] ?? null) ? (float) $input['salary'] : 'NULL';
        Database::iud(
            "INSERT INTO job_posts
                (title, company, description, requirements, location, job_type, category, salary, salary_range, contact_email, contact_phone, application_url, is_active, created_by, created_at, updated_at)
             VALUES
                ('" . self::esc($input['title'] ?? '') . "',
                 '" . self::esc($input['company'] ?? '') . "',
                 '" . self::esc($input['description'] ?? '') . "',
                 '" . self::esc($input['requirements'] ?? '') . "',
                 '" . self::esc($input['location'] ?? '') . "',
                 '" . self::normalizeJobType((string) ($input['jobType'] ?? '')) . "',
                 '" . self::esc($input['category'] ?? '') . "',
                 $salary,
                 '" . self::esc($input['salaryRange'] ?? '') . "',
                 '" . self::esc($input['contactEmail'] ?? '') . "',
                 '" . self::esc($input['contactPhone'] ?? '') . "',
                 '" . self::esc($input['applicationUrl'] ?? '') . "',
                 1,
                 " . max(1, $adminId) . ",
                 NOW(),
                 NOW())"
        );
        return true;
    }

    public static function updateJobPost(int $jobId, array $input): bool
    {
        $salary = is_numeric($input['salary'] ?? null) ? (float) $input['salary'] : 'NULL';
        $isActive = !empty($input['isActive']) ? 1 : 0;
        Database::iud(
            "UPDATE job_posts
             SET title = '" . self::esc($input['title'] ?? '') . "',
                 company = '" . self::esc($input['company'] ?? '') . "',
                 description = '" . self::esc($input['description'] ?? '') . "',
                 requirements = '" . self::esc($input['requirements'] ?? '') . "',
                 location = '" . self::esc($input['location'] ?? '') . "',
                 job_type = '" . self::normalizeJobType((string) ($input['jobType'] ?? '')) . "',
                 category = '" . self::esc($input['category'] ?? '') . "',
                 salary = $salary,
                 salary_range = '" . self::esc($input['salaryRange'] ?? '') . "',
                 contact_email = '" . self::esc($input['contactEmail'] ?? '') . "',
                 contact_phone = '" . self::esc($input['contactPhone'] ?? '') . "',
                 application_url = '" . self::esc($input['applicationUrl'] ?? '') . "',
                 is_active = $isActive,
                 updated_at = NOW()
             WHERE job_id = $jobId"
        );
        return true;
    }

    public static function deleteJobPost(int $jobId): bool
    {
        Database::iud("DELETE FROM job_posts WHERE job_id = $jobId");
        return true;
    }

    public static function getHelpCenters(array $filters = []): array
    {
        $where = ['1=1'];
        $type = trim((string) ($filters['type'] ?? 'all'));
        if ($type !== '' && $type !== 'all') {
            $where[] = "type = '" . self::esc($type) . "'";
        }

        $category = trim((string) ($filters['centerCategory'] ?? 'all'));
        if ($category !== '' && $category !== 'all') {
            $where[] = "category = '" . self::esc($category) . "'";
        }

        $status = trim((string) ($filters['centerStatus'] ?? 'all'));
        if ($status === 'active') {
            $where[] = 'is_active = 1';
        } elseif ($status === 'inactive') {
            $where[] = 'is_active = 0';
        }

        $rs = Database::search(
            "SELECT *
             FROM help_centers
             WHERE " . implode(' AND ', $where) . "
             ORDER BY created_at DESC, help_center_id DESC"
        );

        $items = [];
        while ($rs && ($row = $rs->fetch_assoc())) {
            $items[] = [
                'helpCenterId' => (int) $row['help_center_id'],
                'name' => $row['name'] ?? '',
                'organization' => $row['organization'] ?? '',
                'type' => $row['type'] ?? '',
                'category' => $row['category'] ?? '',
                'phoneNumber' => $row['phone_number'] ?? '',
                'email' => $row['email'] ?? '',
                'website' => $row['website'] ?? '',
                'address' => $row['address'] ?? '',
                'city' => $row['city'] ?? '',
                'state' => $row['state'] ?? '',
                'zipCode' => $row['zip_code'] ?? '',
                'availability' => $row['availability'] ?? '',
                'description' => $row['description'] ?? '',
                'specialties' => $row['specialties'] ?? '',
                'active' => !empty($row['is_active']),
            ];
        }

        return $items;
    }

    public static function getHelpCenterById(int $helpCenterId): ?array
    {
        foreach (self::getHelpCenters() as $center) {
            if ($center['helpCenterId'] === $helpCenterId) {
                return $center;
            }
        }
        return null;
    }

    public static function createHelpCenter(array $input, int $adminUserId): bool
    {
        $isActive = !empty($input['isActive']) ? 1 : 0;
        Database::iud(
            "INSERT INTO help_centers
                (name, organization, type, category, phone_number, email, website, address, city, state, zip_code, availability, description, specialties, is_active, created_by, created_at, updated_at)
             VALUES
                ('" . self::esc($input['name'] ?? '') . "',
                 '" . self::esc($input['organization'] ?? '') . "',
                 '" . self::esc($input['type'] ?? '') . "',
                 '" . self::esc($input['category'] ?? '') . "',
                 '" . self::esc($input['phoneNumber'] ?? '') . "',
                 '" . self::esc($input['email'] ?? '') . "',
                 '" . self::esc($input['website'] ?? '') . "',
                 '" . self::esc($input['address'] ?? '') . "',
                 '" . self::esc($input['city'] ?? '') . "',
                 '" . self::esc($input['state'] ?? '') . "',
                 '" . self::esc($input['zipCode'] ?? '') . "',
                 '" . self::esc($input['availability'] ?? '') . "',
                 '" . self::esc($input['description'] ?? '') . "',
                 '" . self::esc($input['specialties'] ?? '') . "',
                 $isActive,
                 " . max(1, $adminUserId) . ",
                 NOW(),
                 NOW())"
        );
        return true;
    }

    public static function updateHelpCenter(int $helpCenterId, array $input): bool
    {
        $isActive = !empty($input['isActive']) ? 1 : 0;
        Database::iud(
            "UPDATE help_centers
             SET name = '" . self::esc($input['name'] ?? '') . "',
                 organization = '" . self::esc($input['organization'] ?? '') . "',
                 type = '" . self::esc($input['type'] ?? '') . "',
                 category = '" . self::esc($input['category'] ?? '') . "',
                 phone_number = '" . self::esc($input['phoneNumber'] ?? '') . "',
                 email = '" . self::esc($input['email'] ?? '') . "',
                 website = '" . self::esc($input['website'] ?? '') . "',
                 address = '" . self::esc($input['address'] ?? '') . "',
                 city = '" . self::esc($input['city'] ?? '') . "',
                 state = '" . self::esc($input['state'] ?? '') . "',
                 zip_code = '" . self::esc($input['zipCode'] ?? '') . "',
                 availability = '" . self::esc($input['availability'] ?? '') . "',
                 description = '" . self::esc($input['description'] ?? '') . "',
                 specialties = '" . self::esc($input['specialties'] ?? '') . "',
                 is_active = $isActive,
                 updated_at = NOW()
             WHERE help_center_id = $helpCenterId"
        );
        return true;
    }

    public static function deleteHelpCenter(int $helpCenterId): bool
    {
        Database::iud("DELETE FROM help_centers WHERE help_center_id = $helpCenterId");
        return true;
    }

    public static function getSupportGroups(array $filters = []): array
    {
        $where = ['1=1'];
        $status = trim((string) ($filters['status'] ?? 'all'));
        if ($status === 'Active') {
            $where[] = 'sg.is_active = 1';
        } elseif ($status === 'Archived') {
            $where[] = 'sg.is_active = 0';
        }
        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $safeSearch = self::esc($search);
            $where[] = "(sg.name LIKE '%$safeSearch%' OR sg.description LIKE '%$safeSearch%')";
        }

        $rs = Database::search(
            "SELECT sg.*, COUNT(sgm.membership_id) AS member_count, a.full_name AS admin_name
             FROM support_groups sg
             LEFT JOIN support_group_members sgm ON sgm.group_id = sg.group_id
             LEFT JOIN admin a ON a.admin_id = sg.created_by
             WHERE " . implode(' AND ', $where) . "
             GROUP BY sg.group_id
             ORDER BY sg.created_at DESC"
        );

        $groups = [];
        while ($rs && ($row = $rs->fetch_assoc())) {
            $groups[] = [
                'groupId' => (int) $row['group_id'],
                'groupName' => $row['name'] ?? '',
                'description' => $row['description'] ?? '',
                'type' => !empty($row['meeting_link']) ? 'Public' : 'Private',
                'members' => (int) ($row['member_count'] ?? 0),
                'nextSession' => $row['meeting_schedule'] ?? 'To be scheduled',
                'createdBy' => $row['admin_name'] ?? 'Admin',
                'status' => !empty($row['is_active']) ? 'Active' : 'Archived',
            ];
        }

        return $groups;
    }

    public static function getUpcomingSupportGroupSessions(): array
    {
        return [
            ['day' => 'Mon', 'time' => '6:00 PM', 'title' => 'Peer Support Circle'],
            ['day' => 'Wed', 'time' => '7:30 PM', 'title' => 'Family Recovery Group'],
            ['day' => 'Sat', 'time' => '10:00 AM', 'title' => 'New Members Orientation'],
        ];
    }

    public static function getRecoveryTemplates(array $filters = []): array
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $category = trim((string) ($filters['category'] ?? 'all'));
        $where = ["plan_type = 'counselor'"];
        if ($category !== '' && $category !== 'all') {
            $where[] = "category = '" . self::esc($category) . "'";
        }
        if ($search !== '') {
            $safeSearch = self::esc($search);
            $where[] = "title LIKE '%$safeSearch%'";
        }

        $rs = Database::search(
            "SELECT title, description, COALESCE(category, 'General') AS category, updated_at, progress_percentage
             FROM recovery_plans
             WHERE " . implode(' AND ', $where) . "
             ORDER BY updated_at DESC"
        );

        $plans = [];
        while ($rs && ($row = $rs->fetch_assoc())) {
            $plans[] = [
                'planName' => $row['title'] ?? '',
                'description' => $row['description'] ?? '',
                'category' => $row['category'] ?? 'General',
                'adoptionRate' => (int) ($row['progress_percentage'] ?? 0),
                'createdBy' => 'Counselor',
                'lastUpdated' => !empty($row['updated_at']) ? date('M j, Y', strtotime($row['updated_at'])) : '-',
            ];
        }

        return $plans;
    }

    public static function getOnboardingQuestions(array $filters = []): array
    {
        $questions = [
            ['question' => 'What is your primary recovery goal right now?', 'questionType' => 'Text', 'rating' => 4.8, 'status' => 'Display', 'createdOn' => 'Jan 4, 2026'],
            ['question' => 'How often do cravings interrupt your routine?', 'questionType' => 'Scale', 'rating' => 4.6, 'status' => 'Display', 'createdOn' => 'Jan 6, 2026'],
            ['question' => 'Do you currently have a support system?', 'questionType' => 'Yes/No', 'rating' => 4.4, 'status' => 'Display', 'createdOn' => 'Jan 8, 2026'],
            ['question' => 'Which triggers affect you most?', 'questionType' => 'Multiple Choice', 'rating' => 4.3, 'status' => 'Hidden', 'createdOn' => 'Jan 10, 2026'],
        ];

        $search = strtolower(trim((string) ($filters['search'] ?? '')));
        $status = trim((string) ($filters['status'] ?? 'all'));

        return array_values(array_filter($questions, static function (array $question) use ($search, $status): bool {
            if ($search !== '' && !str_contains(strtolower($question['question']), $search)) {
                return false;
            }
            if ($status !== '' && $status !== 'all' && strcasecmp($question['status'], $status) !== 0) {
                return false;
            }
            return true;
        }));
    }

    public static function getReportedContent(array $filters = []): array
    {
        $items = [
            ['contentPreview' => 'User post describing relapse experience with aggressive language', 'authorName' => 'pasidu', 'type' => 'Post', 'reason' => 'Harassment', 'reportedByName' => 'Asha K.', 'date' => 'Mar 21, 2026', 'status' => 'pending'],
            ['contentPreview' => 'Comment promoting an unrelated product link', 'authorName' => 'guest_user', 'type' => 'Comment', 'reason' => 'Spam', 'reportedByName' => 'Ravindu', 'date' => 'Mar 22, 2026', 'status' => 'removed'],
            ['contentPreview' => 'Post sharing unsupported medical claims', 'authorName' => 'wellness_now', 'type' => 'Post', 'reason' => 'Misinformation', 'reportedByName' => 'Maya', 'date' => 'Mar 23, 2026', 'status' => 'pending'],
        ];

        return array_values(array_filter($items, static function (array $item) use ($filters): bool {
            $type = trim((string) ($filters['type'] ?? 'all'));
            $reason = trim((string) ($filters['reason'] ?? 'all'));
            $status = trim((string) ($filters['status'] ?? 'all'));
            if ($type !== '' && $type !== 'all' && strcasecmp($item['type'], $type) !== 0) {
                return false;
            }
            if ($reason !== '' && $reason !== 'all' && strcasecmp($item['reason'], $reason) !== 0) {
                return false;
            }
            if ($status !== '' && $status !== 'all' && strcasecmp($item['status'], $status) !== 0) {
                return false;
            }
            return true;
        }));
    }

    public static function getAnalyticsSummary(): array
    {
        $users = Database::search("SELECT COUNT(*) AS total FROM users WHERE role = 'user'");
        $sessions = Database::search("SELECT COUNT(*) AS total FROM sessions");
        $plans = Database::search("SELECT COUNT(*) AS total, COALESCE(AVG(progress_percentage), 0) AS avg_progress FROM recovery_plans");
        $jobs = Database::search("SELECT COUNT(*) AS total FROM job_posts WHERE is_active = 1");

        $usersRow = $users ? ($users->fetch_assoc() ?: []) : [];
        $sessionsRow = $sessions ? ($sessions->fetch_assoc() ?: []) : [];
        $plansRow = $plans ? ($plans->fetch_assoc() ?: []) : [];
        $jobsRow = $jobs ? ($jobs->fetch_assoc() ?: []) : [];

        return [
            'totalUsers' => (int) ($usersRow['total'] ?? 0),
            'totalSessions' => (int) ($sessionsRow['total'] ?? 0),
            'totalPlans' => (int) ($plansRow['total'] ?? 0),
            'avgPlanProgress' => round((float) ($plansRow['avg_progress'] ?? 0), 1),
            'activeJobs' => (int) ($jobsRow['total'] ?? 0),
        ];
    }

    public static function getFinanceSummary(): array
    {
        $revenue = Database::search("SELECT COALESCE(SUM(amount), 0) AS total, COUNT(*) AS sessions_paid, COALESCE(AVG(amount), 0) AS avg_payment FROM transactions WHERE status = 'completed'");
        $pendingRefunds = Database::search("SELECT COUNT(*) AS total FROM refund_disputes WHERE status = 'pending'");
        $row = $revenue ? ($revenue->fetch_assoc() ?: []) : [];
        $refundRow = $pendingRefunds ? ($pendingRefunds->fetch_assoc() ?: []) : [];

        return [
            'totalRevenue' => (float) ($row['total'] ?? 0),
            'sessionsPaid' => (int) ($row['sessions_paid'] ?? 0),
            'avgPayment' => (float) ($row['avg_payment'] ?? 0),
            'pendingRefunds' => (int) ($refundRow['total'] ?? 0),
        ];
    }

    public static function getRefundDisputes(array $filters = []): array
    {
        $where = ['1=1'];
        $status = trim((string) ($filters['disputeStatus'] ?? 'all'));
        if ($status !== '' && $status !== 'all') {
            $where[] = "rd.status = '" . self::esc($status) . "'";
        }
        $issue = trim((string) ($filters['disputeIssue'] ?? 'allIssues'));
        if ($issue !== '' && $issue !== 'allIssues') {
            $where[] = "rd.issue_type = '" . self::esc(strtolower(str_replace(' ', '_', $issue))) . "'";
        }

        $rs = Database::search(
            "SELECT rd.*, t.transaction_uuid, t.amount,
                    COALESCE(u.display_name, u.email) AS user_name,
                    COALESCE(cu.display_name, cu.email, 'Unassigned') AS counselor_name
             FROM refund_disputes rd
             INNER JOIN transactions t ON t.transaction_id = rd.transaction_id
             INNER JOIN users u ON u.user_id = rd.user_id
             LEFT JOIN counselors c ON c.counselor_id = t.counselor_id
             LEFT JOIN users cu ON cu.user_id = c.user_id
             WHERE " . implode(' AND ', $where) . "
             ORDER BY rd.created_at DESC"
        );

        $items = [];
        while ($rs && ($row = $rs->fetch_assoc())) {
            $items[] = [
                'transactionId' => $row['transaction_uuid'] ?: ('TXN-' . $row['transaction_id']),
                'userName' => $row['user_name'] ?? 'User',
                'counselorName' => $row['counselor_name'] ?? 'Unassigned',
                'amount' => number_format((float) ($row['amount'] ?? 0), 2),
                'issue' => ucwords(str_replace('_', ' ', (string) ($row['issue_type'] ?? 'other'))),
                'status' => $row['status'] ?? 'pending',
            ];
        }

        if ($items === []) {
            $items[] = [
                'transactionId' => 'TXN-DEMO-01',
                'userName' => 'Demo User',
                'counselorName' => 'Demo Counselor',
                'amount' => '45.00',
                'issue' => 'Technical Issue',
                'status' => 'pending',
            ];
        }

        return $items;
    }

    public static function getTransactions(string $search = ''): array
    {
        $where = ['1=1'];
        if ($search !== '') {
            $safeSearch = self::esc($search);
            $where[] = "(t.transaction_uuid LIKE '%$safeSearch%' OR u.email LIKE '%$safeSearch%' OR COALESCE(cu.email, '') LIKE '%$safeSearch%')";
        }

        $rs = Database::search(
            "SELECT t.*, COALESCE(u.display_name, u.email) AS user_name,
                    COALESCE(cu.display_name, cu.email, 'Unassigned') AS counselor_name
             FROM transactions t
             INNER JOIN users u ON u.user_id = t.user_id
             LEFT JOIN counselors c ON c.counselor_id = t.counselor_id
             LEFT JOIN users cu ON cu.user_id = c.user_id
             WHERE " . implode(' AND ', $where) . "
             ORDER BY t.created_at DESC"
        );

        $items = [];
        while ($rs && ($row = $rs->fetch_assoc())) {
            $items[] = [
                'transactionId' => $row['transaction_uuid'] ?: ('TXN-' . $row['transaction_id']),
                'userName' => $row['user_name'] ?? 'User',
                'counselorName' => $row['counselor_name'] ?? 'Unassigned',
                'date' => !empty($row['created_at']) ? date('M j, Y', strtotime($row['created_at'])) : '-',
                'amount' => number_format((float) ($row['amount'] ?? 0), 2),
                'paymentMethod' => ucfirst((string) ($row['payment_type'] ?? 'session')),
                'status' => $row['status'] ?? 'pending',
            ];
        }
        return $items;
    }

    public static function getSystemSettings(): array
    {
        $defaults = [
            'platform_name' => 'New Path Recovery',
            'session_length' => '60',
            'email_notifications' => '1',
            'sms_notifications' => '0',
            'current_logo' => 'logo.svg',
        ];

        $rs = Database::search("SELECT setting_key, setting_value FROM system_settings");
        while ($rs && ($row = $rs->fetch_assoc())) {
            $defaults[$row['setting_key']] = (string) ($row['setting_value'] ?? '');
        }

        return [
            'platformName' => $defaults['platform_name'],
            'sessionLength' => (int) $defaults['session_length'],
            'emailNotificationsEnabled' => $defaults['email_notifications'] === '1',
            'smsNotificationsEnabled' => $defaults['sms_notifications'] === '1',
            'currentLogo' => $defaults['current_logo'],
        ];
    }

    public static function saveSystemSettings(array $input, int $adminUserId): bool
    {
        $entries = [
            'platform_name' => trim((string) ($input['platformName'] ?? 'New Path Recovery')),
            'session_length' => trim((string) ($input['sessionLength'] ?? '60')),
            'email_notifications' => !empty($input['emailNotifications']) ? '1' : '0',
            'sms_notifications' => !empty($input['smsNotifications']) ? '1' : '0',
            'current_logo' => 'logo.svg',
        ];

        foreach ($entries as $key => $value) {
            $safeKey = self::esc($key);
            $safeValue = self::esc($value);
            Database::iud(
                "INSERT INTO system_settings (setting_key, setting_value, updated_by, updated_at)
                 VALUES ('$safeKey', '$safeValue', $adminUserId, NOW())
                 ON DUPLICATE KEY UPDATE setting_value = '$safeValue', updated_by = $adminUserId, updated_at = NOW()"
            );
        }

        return true;
    }

    public static function getAdminRoles(): array
    {
        $rs = Database::search(
            "SELECT a.full_name, a.permissions, a.is_super_admin, COUNT(a2.admin_id) AS assigned_admins
             FROM admin a
             LEFT JOIN admin a2 ON a2.is_super_admin = a.is_super_admin
             GROUP BY a.admin_id
             ORDER BY a.is_super_admin DESC, a.full_name ASC"
        );

        $roles = [];
        while ($rs && ($row = $rs->fetch_assoc())) {
            $roles[] = [
                'roleName' => !empty($row['is_super_admin']) ? 'Super Admin' : 'Platform Admin',
                'permissions' => !empty($row['permissions']) ? (json_decode($row['permissions'], true) ?: ['Manage Platform']) : (!empty($row['is_super_admin']) ? ['All'] : ['Users', 'Content', 'Reports']),
                'assignedAdmins' => (int) ($row['assigned_admins'] ?? 1),
            ];
        }

        if ($roles === []) {
            $roles[] = ['roleName' => 'Super Admin', 'permissions' => ['All'], 'assignedAdmins' => 1];
        }

        return $roles;
    }

    public static function getAuditLogs(array $filters = []): array
    {
        $where = ['1=1'];
        $action = trim((string) ($filters['action'] ?? 'all'));
        if ($action !== '' && $action !== 'all') {
            $where[] = "action = '" . self::esc($action) . "'";
        }
        $startDate = trim((string) ($filters['startDate'] ?? ''));
        if ($startDate !== '') {
            $where[] = "DATE(created_at) >= '" . self::esc($startDate) . "'";
        }
        $endDate = trim((string) ($filters['endDate'] ?? ''));
        if ($endDate !== '') {
            $where[] = "DATE(created_at) <= '" . self::esc($endDate) . "'";
        }

        $rs = Database::search(
            "SELECT al.*, COALESCE(u.display_name, u.email, 'System Admin') AS admin_name
             FROM audit_logs al
             LEFT JOIN users u ON u.user_id = al.user_id
             WHERE " . implode(' AND ', $where) . "
             ORDER BY al.created_at DESC
             LIMIT 50"
        );

        $logs = [];
        while ($rs && ($row = $rs->fetch_assoc())) {
            $logs[] = [
                'dateTime' => !empty($row['created_at']) ? date('M j, Y g:i A', strtotime($row['created_at'])) : '-',
                'action' => $row['action'] ?? 'System event',
                'adminName' => $row['admin_name'] ?? 'Admin',
                'affectedResource' => trim(($row['entity_type'] ?? 'resource') . ' #' . ($row['entity_id'] ?? '-')),
                'status' => 'Completed',
            ];
        }

        if ($logs === []) {
            $logs[] = [
                'dateTime' => date('M j, Y g:i A'),
                'action' => 'Viewed admin dashboard',
                'adminName' => 'System Admin',
                'affectedResource' => 'dashboard',
                'status' => 'Completed',
            ];
        }

        return $logs;
    }
}
