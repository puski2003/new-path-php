<?php

$plans = CounselorRecoveryPlansModel::getAll((int) ($user['counselorId'] ?? 0));
