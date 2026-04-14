<?php
require_once __DIR__ . '/../job-posts/job-posts.model.php';
require_once __DIR__ . '/../help-center/help-center.model.php';

class ResourcesModel
{
    public static function getData(string $tab, array $filters, int $page = 1, int $perPage = 15): array
    {
        $jobPostsResult = JobPostsModel::getJobPostsPaginated($filters, $page, $perPage);
        $helpCentersResult = HelpCenterModel::getHelpCentersPaginated($filters, $page, $perPage);

        $programs = [
            ['programName' => 'Digital Skills Bootcamp', 'provider' => 'SkillUp', 'category' => 'Technology', 'duration' => '12 weeks', 'format' => 'Online', 'cost' => 'Free', 'status' => 'approved'],
            ['programName' => 'Hospitality Readiness', 'provider' => 'BrightStart', 'category' => 'Hospitality', 'duration' => '6 weeks', 'format' => 'Hybrid', 'cost' => '$120', 'status' => 'pending'],
        ];
        $programsCount = count($programs);
        $programsMeta = Pagination::meta($programsCount, 1, $perPage);
        $programsPagination = [
            'currentPage' => 1,
            'totalPages' => 1,
            'totalRows' => $programsCount,
            'fromRow' => 1,
            'toRow' => $programsCount,
            'offset' => 0,
            'perPage' => $perPage,
        ];

        $pendingPrograms = [
            ['programName' => 'Workplace Confidence', 'providerName' => 'FutureWorks', 'format' => 'Online', 'duration' => '4 weeks', 'submittedTime' => '2 days ago'],
            ['programName' => 'Retail Skills Track', 'providerName' => 'NextStep', 'format' => 'In Person', 'duration' => '8 weeks', 'submittedTime' => '4 days ago'],
        ];
        $pendingCount = count($pendingPrograms);
        $pendingProgramsPagination = [
            'currentPage' => 1,
            'totalPages' => 1,
            'totalRows' => $pendingCount,
            'fromRow' => 1,
            'toRow' => $pendingCount,
            'offset' => 0,
            'perPage' => $perPage,
        ];

        return [
            'jobPosts' => $jobPostsResult,
            'helpCenters' => $helpCentersResult,
            'programs' => $programs,
            'programsPagination' => $programsPagination,
            'pendingPrograms' => $pendingPrograms,
            'pendingProgramsPagination' => $pendingProgramsPagination,
        ];
    }
}
