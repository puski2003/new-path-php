<?php

$clients = CounselorClientsModel::getAll((int) ($user['counselorId'] ?? 0));
$query=trim(Request::get('q') ?? '');
if(!empty($query)){
    $clients=CounselorClientsModel::searchClientsByName($query,$user['counselorId']);
}
 
?><script>console.log(<?php echo json_encode($clients ?? null); ?>)</script><?php
 