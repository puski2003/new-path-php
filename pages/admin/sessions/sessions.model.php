<?php
require_once __DIR__ . '/../common/admin.data.php';
require_once __DIR__ . '/../../../core/GoogleMeetService.php';

class AdminSessionsModel
{
    /**
     * Paginated list of all sessions with user + counselor names.
     *
     * @param  string $search  Optional search term (user name, counselor name, status)
     * @param  int    $limit
     * @param  int    $offset
     * @return array{ rows: array, total: int }
     */
    public static function getSessions(string $search = '', int $limit = 20, int $offset = 0): array
    {
        $safeSearch = addslashes($search);
        $where = $search !== ''
            ? "WHERE (
                  COALESCE(u.display_name, CONCAT(u.first_name,' ',u.last_name), u.username) LIKE '%$safeSearch%'
               OR COALESCE(cu.display_name, CONCAT(cu.first_name,' ',cu.last_name), cu.username) LIKE '%$safeSearch%'
               OR s.status LIKE '%$safeSearch%'
               )"
            : '';

        $countRs = Database::search(
            "SELECT COUNT(*) AS total
             FROM sessions s
             JOIN users u  ON u.user_id  = s.user_id
             JOIN counselors c ON c.counselor_id = s.counselor_id
             JOIN users cu ON cu.user_id = c.user_id
             $where"
        );
        $total = (int)(($countRs ? $countRs->fetch_assoc() : [])['total'] ?? 0);

        $rs = Database::search(
            "SELECT
                s.session_id,
                s.session_datetime,
                s.duration_minutes,
                s.session_type,
                s.status,
                s.meeting_link,
                s.meet_space_name,
                s.created_at,
                COALESCE(u.display_name, CONCAT(u.first_name,' ',u.last_name), u.username, 'User') AS user_name,
                u.email AS user_email,
                COALESCE(cu.display_name, CONCAT(cu.first_name,' ',cu.last_name), cu.username, 'Counselor') AS counselor_name,
                cu.email AS counselor_email
             FROM sessions s
             JOIN users u  ON u.user_id  = s.user_id
             JOIN counselors c ON c.counselor_id = s.counselor_id
             JOIN users cu ON cu.user_id = c.user_id
             $where
             ORDER BY s.session_datetime DESC
             LIMIT $limit OFFSET $offset"
        );

        $rows = [];
        if ($rs) {
            while ($row = $rs->fetch_assoc()) {
                $rows[] = $row;
            }
        }

        return ['rows' => $rows, 'total' => $total];
    }

    /**
     * Fetch a single session row (for the meeting detail modal).
     */
    public static function getSession(int $sessionId): ?array
    {
        $rs = Database::search(
            "SELECT
                s.session_id, s.session_datetime, s.duration_minutes,
                s.status, s.meeting_link, s.meet_space_name,
                COALESCE(u.display_name, CONCAT(u.first_name,' ',u.last_name), u.username) AS user_name,
                COALESCE(cu.display_name, CONCAT(cu.first_name,' ',cu.last_name), cu.username) AS counselor_name
             FROM sessions s
             JOIN users u ON u.user_id = s.user_id
             JOIN counselors c ON c.counselor_id = s.counselor_id
             JOIN users cu ON cu.user_id = c.user_id
             WHERE s.session_id = $sessionId
             LIMIT 1"
        );
        $row = $rs ? $rs->fetch_assoc() : null;
        return $row ?: null;
    }

    /**
     * Fetch live meeting details from the Meet API for admin view.
     * Returns null if the session has no space name or the API call fails.
     */
    public static function getMeetingDetails(int $sessionId): ?array
    {
        $session = self::getSession($sessionId);
        if (!$session || empty($session['meet_space_name'])) {
            return null;
        }

        $spaceName = $session['meet_space_name'];

        $space   = GoogleMeetService::getSpaceDetails($spaceName);
        $records = GoogleMeetService::getConferenceRecords($spaceName);

        return [
            'session'         => $session,
            'space'           => $space,
            'conferenceRecords' => $records,
        ];
    }
}
