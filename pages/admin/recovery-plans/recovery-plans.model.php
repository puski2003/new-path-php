<?php

class RecoveryPlansAdminModel
{
    private static function esc(string $value): string
    {
        Database::setUpConnection();
        return Database::$connection->real_escape_string($value);
    }

    public static function getTemplates(array $filters): array
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

    public static function getQuestions(array $filters): array
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
}
