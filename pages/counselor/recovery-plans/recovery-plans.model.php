<?php

require_once __DIR__ . '/../common/counselor.data.php';

class CounselorRecoveryPlansModel
{
    public static function getAll(int $counselorId): array
    {
        return CounselorData::getPlansByCounselor($counselorId);
    }

    public static function searchPlans(string $query,int $counselorId){
        $plans= CounselorData::getPlansByCounselor($counselorId);
        ?><script>console.log(<?php echo json_encode($plans ?? null); ?>)</script><?php
        $filteredPlans=[];
        foreach($plans as $plan){
            if(stripos($plan['title'],$query)!==false){
                $filteredPlans[]=$plan;
            }
        }
        $plans=$filteredPlans;
        ?><script>console.log(<?php echo json_encode($plans ?? null); ?>)</script><?php
        return $plans;
    }
}
