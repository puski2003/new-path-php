<?php

class CounselorsModel
{
    /**
     * Fetch counselors based on search query and filters
     */
    public static function getCounselors($params = [])
    {
        $limit  = (int)($params['limit'] ?? 10);
        $offset = (int)($params['offset'] ?? 0);

        $whereSql = "WHERE u.role = 'counselor'";

        // Search by name
        if (!empty($params['q'])) {
            $q = addslashes($params['q']);
            $whereSql .= " AND u.display_name LIKE '%$q%'";
        }

        // Filter by specialty
        if (!empty($params['specialty'])) {
            $specialty = addslashes($params['specialty']);
            $whereSql .= " AND c.specialty LIKE '%$specialty%'";
        }

        // Filter by min experience
        if (!empty($params['minExperience'])) {
            $minExp = (int) $params['minExperience'];
            $whereSql .= " AND c.experience_years >= $minExp";
        }

        // Filter by max price
        if (!empty($params['maxPrice'])) {
            $maxPrice = (float) $params['maxPrice'];
            $whereSql .= " AND c.consultation_fee <= $maxPrice";
        }

        $sql = "
            SELECT
                c.counselor_id,
                u.display_name AS name,
                u.profile_picture,
                c.specialty AS specialty_short,
                c.experience_years,
                c.consultation_fee,
                (SELECT ROUND(AVG(rating), 1) FROM sessions WHERE counselor_id = c.counselor_id AND rating IS NOT NULL) AS rating_value
            FROM counselors c
            INNER JOIN users u ON c.user_id = u.user_id
            $whereSql
            ORDER BY c.counselor_id DESC
            LIMIT $limit OFFSET $offset
        ";

        $rs = Database::search($sql);
        $counselors = [];

        while ($row = $rs->fetch_assoc()) {
            if ($row['rating_value'] === null) {
                // Determine random fallback rating between 4.0 and 5.0 for UI if none exists
                $row['rating_value'] = rand(40, 50) / 10;
            }
            $counselors[] = $row;
        }

        // Count total for pagination
        $countSql = "SELECT COUNT(*) as total FROM counselors c INNER JOIN users u ON c.user_id = u.user_id $whereSql";
        $countRs = Database::search($countSql);
        $total = $countRs->fetch_assoc()['total'] ?? 0;

        return [
            'data'  => $counselors,
            'total' => $total
        ];
    }

    /**
     * Fetch one counselor for single counselor view.
     */
    public static function getCounselorById(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }

        $counselorId = (int)$id;
        $sql = "
            SELECT
                c.counselor_id,
                u.display_name AS name,
                u.profile_picture,
                c.title,
                c.specialty,
                c.bio,
                c.experience_years,
                c.consultation_fee,
                c.availability_schedule,
                COALESCE(NULLIF(c.rating, 0), (SELECT ROUND(AVG(s.rating), 1) FROM sessions s WHERE s.counselor_id = c.counselor_id AND s.rating IS NOT NULL)) AS rating_value,
                COALESCE(NULLIF(c.total_reviews, 0), (SELECT COUNT(*) FROM sessions s2 WHERE s2.counselor_id = c.counselor_id AND (s2.review IS NOT NULL OR s2.rating IS NOT NULL))) AS total_reviews
            FROM counselors c
            INNER JOIN users u ON c.user_id = u.user_id
            WHERE c.counselor_id = $counselorId AND u.role = 'counselor'
            LIMIT 1
        ";

        $rs = Database::search($sql);
        if (!$rs || $rs->num_rows === 0) {
            return null;
        }

        $row = $rs->fetch_assoc();

        $rating = $row['rating_value'] !== null ? (float)$row['rating_value'] : 4.8;
        if ($rating <= 0) {
            $rating = 4.8;
        }

        $totalReviews = isset($row['total_reviews']) ? (int)$row['total_reviews'] : 150;
        if ($totalReviews <= 0) {
            $totalReviews = 150;
        }

        $price = isset($row['consultation_fee']) ? (float)$row['consultation_fee'] : 0.0;

        return [
            'counselor_id' => (int)$row['counselor_id'],
            'name' => $row['name'] ?? 'Counselor',
            'profile_picture' => !empty($row['profile_picture']) ? $row['profile_picture'] : '/assets/img/avatar.png',
            'title' => !empty($row['title']) ? $row['title'] : 'Counselor',
            'specialty' => !empty($row['specialty']) ? $row['specialty'] : 'Specialist',
            'bio' => !empty($row['bio']) ? $row['bio'] : 'No biography available.',
            'experience_years' => isset($row['experience_years']) ? (int)$row['experience_years'] : 0,
            'consultation_fee' => $price,
            'price_formatted' => 'Rs. ' . number_format($price, 2),
            'rating' => number_format($rating, 1, '.', ''),
            'total_reviews' => $totalReviews,
            'availability_schedule' => !empty($row['availability_schedule']) ? $row['availability_schedule'] : '{}'
        ];
    }

    /**
     * Get distinct specialties for the filter dropdown
     */
    public static function getSpecialties()
    {
        $rs = Database::search("SELECT DISTINCT specialty FROM counselors WHERE specialty IS NOT NULL AND specialty != ''");
        $specialties = [];
        while ($row = $rs->fetch_assoc()) {
            // Split if comma separated or just take the raw string
            $specs = explode(',', $row['specialty']);
            foreach ($specs as $s) {
                $s = trim($s);
                if (!empty($s) && !in_array($s, $specialties, true)) {
                    $specialties[] = $s;
                }
            }
        }
        sort($specialties);
        return $specialties;
    }
}
