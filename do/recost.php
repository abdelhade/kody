<?php
include('../includes/connect.php');
require_once __DIR__ . '/../includes/cost_recalculate.php';

set_time_limit(0);
$recostSuccess = recalculateAverageCosts($conn, true);

header('Location: ../myitems.php?recost=' . ($recostSuccess ? 'ok' : 'fail'));
exit;

