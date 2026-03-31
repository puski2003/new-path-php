<?php
require_once __DIR__ . '/../../common/admin.head.php';
if (Request::isPost()) {
    require_once __DIR__ . '/../../common/admin.data.php';
    $helpCenterId = (int) (Request::post('helpCenterId') ?? 0);
    if ($helpCenterId > 0) {
        AdminData::deleteHelpCenter($helpCenterId);
    }
}
Response::redirect('/admin/resources?tab=help-centers');
