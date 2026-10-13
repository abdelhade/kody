<?php
include('../includes/connect.php');
require_once __DIR__ . '/../includes/cost_recalculate.php';

$recostSuccess = recalculateAverageCosts($conn);

header('Location: ../myitems.php?recost=' . ($recostSuccess ? 'ok' : 'fail'));
exit;

