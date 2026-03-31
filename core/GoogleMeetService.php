<?php

/**
 * GoogleMeetService
 *
 * Generates a Google Meet link by creating a Google Calendar event
 * using the Google Calendar API with OAuth 2.0 (offline / refresh-token flow).
 *
 * No Composer required — uses PHP's built-in cURL extension.
 *
 * Required environment variables (set in your .env file):
 *   GOOGLE_CLIENT_ID       — OAuth 2.0 Client ID
 *   GOOGLE_CLIENT_SECRET   — OAuth 2.0 Client Secret
 *   GOOGLE_REFRESH_TOKEN   — Long-lived refresh token (from one-time OAuth flow)
 *   GOOGLE_CALENDAR_ID     — Calendar to create events on (usually 'primary')
 *
 * See the README or docs for how to obtain these credentials.
 */
class GoogleMeetService
{
    private const TOKEN_URL    = 'https://oauth2.googleapis.com/token';
    private const CALENDAR_URL = 'https://www.googleapis.com/calendar/v3/calendars/{calendarId}/events';

    /**
     * Create a Calendar event with a Google Meet conference and return the
     * Meet link (e.g. "https://meet.google.com/abc-defg-hij").
     *
     * @param string $title         Event title shown in Google Calendar
     * @param string $startDatetime MySQL-format datetime  "2025-08-14 10:00:00"
     * @param int    $durationMin   Session duration in minutes
     * @param string $timeZone      IANA timezone, e.g. "Asia/Colombo"
     * @param string $description   Optional event description
     *
     * @return string|null  Meet link on success, null on any failure
     */
    public static function createMeetLink(
        string  $title,
        string  $startDatetime,
        int     $durationMin    = 60,
        string  $timeZone       = 'Asia/Colombo',
        string  $description    = '',
        ?string $counselorEmail = null
    ): ?string {
        // 1. Check all credentials are configured
        $clientId     = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');
        $refreshToken = env('GOOGLE_REFRESH_TOKEN');
        $calendarId   = env('GOOGLE_CALENDAR_ID', 'primary');

        if (!$clientId || !$clientSecret || !$refreshToken) {
            error_log('[GoogleMeetService] Missing credentials — skipping Meet link generation.');
            return null;
        }

        // 2. Exchange refresh token for a short-lived access token
        $accessToken = self::getAccessToken($clientId, $clientSecret, $refreshToken);
        if (!$accessToken) {
            return null;
        }

        // 3. Build RFC 3339 datetimes
        $startTs = strtotime($startDatetime);
        if ($startTs === false) {
            error_log('[GoogleMeetService] Invalid startDatetime: ' . $startDatetime);
            return null;
        }
        $endTs    = $startTs + ($durationMin * 60);
        $startRfc = self::toRfc3339($startTs, $timeZone);
        $endRfc   = self::toRfc3339($endTs, $timeZone);

        // 4. Build the event payload
        $requestId = bin2hex(random_bytes(8)); // unique per request to avoid duplicates
        $payload   = [
            'summary'          => $title,
            'description'      => $description,
            'start'            => ['dateTime' => $startRfc, 'timeZone' => $timeZone],
            'end'              => ['dateTime' => $endRfc,   'timeZone' => $timeZone],
            'guestsCanModify'  => true,
            'conferenceData'   => [
                'createRequest' => [
                    'requestId'             => $requestId,
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                ],
            ],
        ];

        // Add counselor as organizer-attendee so the session appears on their
        // Google Calendar and they receive host-level controls in the meeting.
        if ($counselorEmail) {
            $payload['organizer']  = ['email' => $counselorEmail];
            $payload['attendees']  = [['email' => $counselorEmail, 'organizer' => true]];
        }

        // 5. POST to Google Calendar API
        $url      = str_replace('{calendarId}', rawurlencode($calendarId), self::CALENDAR_URL);
        $url     .= '?conferenceDataVersion=1';
        $response = self::curlPost($url, $payload, $accessToken);

        if (!$response) {
            return null;
        }

        // 6. Extract the Meet link from the response
        return self::extractMeetLink($response);
    }

    // -------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------

    /**
     * Exchange a refresh token for a fresh access token.
     */
    private static function getAccessToken(
        string $clientId,
        string $clientSecret,
        string $refreshToken
    ): ?string {
        $ch = curl_init(self::TOKEN_URL);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $refreshToken,
                'grant_type'    => 'refresh_token',
            ]),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT        => 10,
        ]);

        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200 || !$body) {
            error_log('[GoogleMeetService] Token exchange failed. HTTP ' . $code . ' — ' . $body);
            return null;
        }

        $data = json_decode($body, true);
        return $data['access_token'] ?? null;
    }

    /**
     * POST JSON to a Google API endpoint with a Bearer token.
     *
     * @return array|null  Decoded JSON response or null on error
     */
    private static function curlPost(string $url, array $payload, string $accessToken): ?array
    {
        $json = json_encode($payload);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $json,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json),
            ],
            CURLOPT_TIMEOUT        => 15,
        ]);

        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code < 200 || $code >= 300 || !$body) {
            error_log('[GoogleMeetService] Calendar API error. HTTP ' . $code . ' — ' . $body);
            return null;
        }

        $data = json_decode($body, true);
        if (!is_array($data)) {
            error_log('[GoogleMeetService] Failed to decode Calendar API response.');
            return null;
        }

        return $data;
    }

    /**
     * Walk the Calendar API event response and find the Meet URI.
     */
    private static function extractMeetLink(array $event): ?string
    {
        // conferenceData.entryPoints[].uri  where entryPointType == 'video'
        $entryPoints = $event['conferenceData']['entryPoints'] ?? [];
        foreach ($entryPoints as $ep) {
            if (($ep['entryPointType'] ?? '') === 'video' && !empty($ep['uri'])) {
                return $ep['uri'];
            }
        }

        // Fallback: hangoutLink at root level
        if (!empty($event['hangoutLink'])) {
            return $event['hangoutLink'];
        }

        error_log('[GoogleMeetService] No Meet link found in response: ' . json_encode($event));
        return null;
    }

    /**
     * Convert a Unix timestamp to RFC 3339 format respecting a given timezone.
     */
    private static function toRfc3339(int $ts, string $timeZone): string
    {
        try {
            $dt = new DateTime('@' . $ts);
            $dt->setTimezone(new DateTimeZone($timeZone));
            return $dt->format(DateTime::RFC3339);
        } catch (Exception $e) {
            // Fallback: UTC
            return gmdate('Y-m-d\TH:i:s\Z', $ts);
        }
    }
}
