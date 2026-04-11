<?php
require_once __DIR__ . '/../job-posts/model.php';
require_once __DIR__ . '/../help-center/model.php';

class ResourcesModel
{
    public static function getData(string $tab, array $filters): array
    {
        return [
            'jobPosts' => $tab === 'job-ads' ? JobPostsModel::getJobPosts($filters) : [],
            'helpCenters' => $tab === 'help-centers' ? HelpCenterModel::getHelpCenters($filters) : [],
            'programs' => [
                ['programName' => 'Digital Skills Bootcamp', 'provider' => 'SkillUp', 'category' => 'Technology', 'duration' => '12 weeks', 'format' => 'Online', 'cost' => 'Free', 'status' => 'approved'],
                ['programName' => 'Hospitality Readiness', 'provider' => 'BrightStart', 'category' => 'Hospitality', 'duration' => '6 weeks', 'format' => 'Hybrid', 'cost' => '$120', 'status' => 'pending'],
            ],
            'pendingPrograms' => [
                ['programName' => 'Workplace Confidence', 'providerName' => 'FutureWorks', 'format' => 'Online', 'duration' => '4 weeks', 'submittedTime' => '2 days ago'],
                ['programName' => 'Retail Skills Track', 'providerName' => 'NextStep', 'format' => 'In Person', 'duration' => '8 weeks', 'submittedTime' => '4 days ago'],
            ],
        ];
    }
}
