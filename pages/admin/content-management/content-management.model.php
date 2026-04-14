<?php

class ContentManagementModel
{
    public static function getReports(array $filters): array
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
}
