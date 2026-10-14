<?php

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $id = (int)($_GET['id'] ?? 0);
    include('../includes/connect.php');
    require_once('../includes/shift_attendance.php');

    $name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
    $shiftstart = $_POST['shiftstart'] ?? '';
    $shiftend = $_POST['shiftend'] ?? '';
    $instart = $_POST['instart'] ?? '';
    $inend = $_POST['inend'] ?? '';
    $outstart = $_POST['outstart'] ?? '';
    $outend = $_POST['outend'] ?? '';

    $latelimit = (int)($_POST['latelimit'] ?? 0);
    $earlylimit = (int)($_POST['earlylimit'] ?? 0);
    $grace1_action = normalize_grace_action($_POST['grace1_action'] ?? 'actual', 'actual');

    $latelimit_2 = (int)($_POST['latelimit_2'] ?? 0);
    $earlylimit_2 = (int)($_POST['earlylimit_2'] ?? 0);
    $grace2_action = normalize_grace_action($_POST['grace2_action'] ?? 'half', 'half');

    $day = [];
    if (isset($_POST['sat'])) { array_push($day, '6'); }
    if (isset($_POST['sun'])) { array_push($day, '7'); }
    if (isset($_POST['mon'])) { array_push($day, '1'); }
    if (isset($_POST['tus'])) { array_push($day, '2'); }
    if (isset($_POST['wed'])) { array_push($day, '3'); }
    if (isset($_POST['thur'])) { array_push($day, '4'); }
    if (isset($_POST['fri'])) { array_push($day, '5'); }
    $workingdays = implode(",", $day);

    // Create DateTime objects for start and end times
    $endTime = new DateTime($shiftend);
    $startTime = new DateTime($shiftstart);

    // Calculate the difference in seconds
    $timeDiffInSeconds = $endTime->getTimestamp() - $startTime->getTimestamp();
    if ($timeDiffInSeconds <= 0) {
        $timeDiffInSeconds += 24 * 3600;
    }

    // Convert the difference to hours
    $hours = round($timeDiffInSeconds / 3600, 2);

    $single_fp_rule = normalize_single_fp_rule($_POST['single_fp_rule'] ?? 'half');
    $ignore_early_in = isset($_POST['ignore_early_in']) ? 1 : 0;
    $ignore_late_out = isset($_POST['ignore_late_out']) ? 1 : 0;
    $flex = shift_flexible_input($_POST);

    $sqlshft = "UPDATE shifts SET 
        name = '$name',
        shiftstart = '$shiftstart',
        shiftend = '$shiftend',
        instart = '$instart',
        inend = '$inend',
        outstart = '$outstart',
        outend = '$outend',
        latelimit = '$latelimit',
        earlylimit = '$earlylimit',
        grace1_action = '$grace1_action',
        latelimit_2 = '$latelimit_2',
        earlylimit_2 = '$earlylimit_2',
        grace2_action = '$grace2_action',
        workingdays = '$workingdays',
        hours = '$hours',
        single_fp_rule = '$single_fp_rule',
        ignore_early_in = '$ignore_early_in',
        ignore_late_out = '$ignore_late_out',
        is_flexible = {$flex['is_flexible']},
        required_hours = {$flex['required_hours']},
        day_cutoff = '{$flex['day_cutoff']}',
        dup_fp_minutes = {$flex['dup_fp_minutes']}
        WHERE id = '$id'";

    $conn->query($sqlshft);
    header('location:../shifts.php');
    exit;
}
