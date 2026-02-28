<?php

class SessionsModel
{
    public static function getSessionsByType(int $userId, string $type, int $page = 1, int $perPage = 5): array
    {
        $safePage = max(1, $page);
        $safePerPage = max(1, min(50, $perPage));
        $offset = ($safePage - 1) * $safePerPage;

        $isUpcoming = $type === 'upcoming';
        $where = $isUpcoming
            ? "s.session_datetime >= NOW() AND s.status IN ('scheduled','confirmed','in_progress')"
            : "(s.session_datetime < NOW() OR s.status IN ('completed','cancelled','no_show'))";

        $order = $isUpcoming ? 's.session_datetime ASC' : 's.session_datetime DESC';

        $countRs = Database::search(
            "SELECT COUNT(*) AS total
             FROM sessions s
             WHERE s.user_id = $userId
               AND $where"
        );
        $countRow = $countRs->fetch_assoc();
        $total = (int)($countRow['total'] ?? 0);

        $rs = Database::search(
            "SELECT s.session_id, s.counselor_id, s.session_datetime, s.session_type, s.status, s.location, s.meeting_link,
                    c.title AS counselor_title, c.specialty,
                    COALESCE(u.display_name, CONCAT(u.first_name, ' ', u.last_name), u.username, 'Counselor') AS counselor_name
             FROM sessions s
             JOIN counselors c ON c.counselor_id = s.counselor_id
             JOIN users u ON u.user_id = c.user_id
             WHERE s.user_id = $userId
               AND $where
             ORDER BY $order
             LIMIT $safePerPage OFFSET $offset"
        );

        $items = [];
        while ($row = $rs->fetch_assoc()) {
            $items[] = self::mapSessionCard($row, $isUpcoming ? 'upcoming' : 'history');
        }

        return [
            'items' => $items,
            'total' => $total,
            'page' => $safePage,
            'totalPages' => max(1, (int)ceil($total / $safePerPage)),
        ];
    }

    public static function getSessionById(int $userId, int $sessionId): ?array
    {
        if ($sessionId <= 0) return null;

        $rs = Database::search(
            "SELECT s.session_id, s.user_id, s.counselor_id, s.session_datetime, s.duration_minutes,
                    s.session_type, s.status, s.location, s.meeting_link, s.session_notes, s.created_at, s.updated_at,
                    c.title AS counselor_title, c.specialty, c.bio,
                    COALESCE(u.display_name, CONCAT(u.first_name, ' ', u.last_name), u.username, 'Counselor') AS counselor_name,
                    u.profile_picture
             FROM sessions s
             JOIN counselors c ON c.counselor_id = s.counselor_id
             JOIN users u ON u.user_id = c.user_id
             WHERE s.user_id = $userId
               AND s.session_id = $sessionId
             LIMIT 1"
        );

        $row = $rs->fetch_assoc();
        if (!$row) return null;

        $transaction = null;
        $txRs = Database::search(
            "SELECT t.transaction_uuid, t.payment_method_id, t.processed_at, t.created_at
             FROM transactions t
             WHERE t.session_id = $sessionId
             ORDER BY t.created_at DESC
             LIMIT 1"
        );
        $tx = $txRs->fetch_assoc();
        if ($tx) {
            $transaction = $tx;
        }

        $cardLast4 = '6714';
        $cardExpiry = '03/25';
        if (!empty($transaction['payment_method_id'])) {
            $paymentMethodId = (int)$transaction['payment_method_id'];
            $pmRs = Database::search(
                "SELECT card_last_four, expiry_month, expiry_year
                 FROM payment_methods
                 WHERE payment_method_id = $paymentMethodId
                 LIMIT 1"
            );
            $pm = $pmRs->fetch_assoc();
            if ($pm) {
                if (!empty($pm['card_last_four'])) {
                    $cardLast4 = (string)$pm['card_last_four'];
                }
                if (!empty($pm['expiry_month']) && !empty($pm['expiry_year'])) {
                    $cardExpiry = str_pad((string)$pm['expiry_month'], 2, '0', STR_PAD_LEFT) . '/' . substr((string)$pm['expiry_year'], -2);
                }
            }
        }

        $sessionDateTime = strtotime((string)$row['session_datetime']);
        $joinWindow = $sessionDateTime ? date('Y-m-d H:i', $sessionDateTime - (15 * 60)) : null;

        return [
            'sessionId' => (int)$row['session_id'],
            'counselorId' => (int)$row['counselor_id'],
            'doctorName' => $row['counselor_name'] ?? 'Dr. Amelia Harper',
            'doctorTitle' => $row['counselor_title'] ?: 'Licensed Clinical Social Worker',
            'specialization' => $row['specialty'] ?: 'Specializes in addiction recovery and trauma-informed care',
            'profilePicture' => $row['profile_picture'] ?: '/assets/img/avatar.png',
            'sessionTypeRaw' => $row['session_type'] ?? 'video',
            'sessionType' => self::formatSessionType((string)($row['session_type'] ?? 'video')),
            'status' => $row['status'] ?? 'scheduled',
            'location' => $row['location'] ?: ucfirst((string)($row['session_type'] ?? 'video')),
            'bookingId' => !empty($transaction['transaction_uuid']) ? $transaction['transaction_uuid'] : ('S' . str_pad((string)$row['session_id'], 10, '0', STR_PAD_LEFT)),
            'bookedAt' => !empty($row['created_at']) ? date('Y-m-d H:i', strtotime($row['created_at'])) . ' Asia/Colombo' : '2025-09-01 10:00 Asia/Colombo',
            'paymentCaptured' => !empty($transaction['processed_at'])
                ? date('Y-m-d H:i', strtotime($transaction['processed_at'])) . ' Asia/Colombo'
                : (!empty($transaction['created_at']) ? date('Y-m-d H:i', strtotime($transaction['created_at'])) . ' Asia/Colombo' : '2025-09-01 10:05 Asia/Colombo'),
            'joinWindow' => $joinWindow ? ($joinWindow . ' Asia/Colombo') : '2025-09-01 14:15 Asia/Colombo',
            'notes' => $row['session_notes'] ?: 'Discussing strategies for managing stress and improving communication in relationships.',
            'cardNumber' => '**** ' . $cardLast4,
            'cardExpiry' => $cardExpiry,
            'meetingLink' => $row['meeting_link'] ?: '',
            'sessionDateTime' => $row['session_datetime'],
        ];
    }

    private static function mapSessionCard(array $row, string $type): array
    {
        $sessionDate = strtotime((string)$row['session_datetime']);
        $formattedDayTime = $sessionDate ? date('D g:ia', $sessionDate) : 'Wed 2pm';
        $formattedDayTime = str_replace('am', 'am', str_replace('pm', 'pm', $formattedDayTime));

        $schedule = $type === 'upcoming'
            ? (self::formatSessionTypeShort((string)($row['session_type'] ?? 'video')) . ' - ' . $formattedDayTime)
            : ('Completed - ' . $formattedDayTime);

        return [
            'sessionId' => (int)$row['session_id'],
            'counselorId' => (int)$row['counselor_id'],
            'doctorName' => $row['counselor_name'] ?? 'Dr. Amelia Harper',
            'specialty' => $row['specialty'] ?: 'Addiction Specialist',
            'schedule' => $schedule,
            'sessionType' => $type,
            'meetingLink' => $row['meeting_link'] ?? '',
        ];
    }

    private static function formatSessionType(string $sessionType): string
    {
        return match ($sessionType) {
            'video' => '1:1 Video',
            'audio' => '1:1 Audio',
            'chat' => '1:1 Chat',
            'in_person' => 'In Person',
            default => '1:1',
        };
    }

    private static function formatSessionTypeShort(string $sessionType): string
    {
        return match ($sessionType) {
            'video' => 'Video',
            'audio' => 'Audio',
            'chat' => 'Chat',
            'in_person' => 'In Person',
            default => 'Session',
        };
    }
}

