<?php
/**
 * AJAX handler for employee targets (save / load)
 * POST action=save  → upsert target
 * GET  action=load  → load targets for an employee
 */
include('../includes/connect.php');

header('Content-Type: application/json; charset=utf-8');

// Ensure the table exists
$conn->query("
    CREATE TABLE IF NOT EXISTS `employee_targets` (
        `id`          INT(11)        NOT NULL AUTO_INCREMENT,
        `employee_id` INT(11)        NOT NULL,
        `period_type` ENUM('monthly','yearly') NOT NULL DEFAULT 'monthly',
        `year`        SMALLINT(4)    NOT NULL,
        `month`       TINYINT(2)     NULL COMMENT 'NULL when period_type=yearly',
        `target_value` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
        `notes`       VARCHAR(255)   NULL,
        `crtime`      TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `mdtime`      TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `user`        INT(11)        NOT NULL DEFAULT 1,
        `tenant`      INT(11)        NOT NULL DEFAULT 0,
        `branch`      INT(11)        NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_emp_target` (`employee_id`,`period_type`,`year`,`month`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
");

$action = $_REQUEST['action'] ?? '';

/* ─────────────────────────────────────────
   SAVE  (POST)
───────────────────────────────────────── */
if ($action === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id  = (int)   ($_POST['employee_id']  ?? 0);
    $period_type  =         ($_POST['period_type']  ?? 'monthly');
    $year         = (int)   ($_POST['year']         ?? date('Y'));
    $month        = isset($_POST['month']) && $_POST['month'] !== '' ? (int)$_POST['month'] : null;
    $target_value = (float) ($_POST['target_value'] ?? 0);
    $notes        = trim(   $_POST['notes']         ?? '');
    $user         = (int)   ($userid ?? 1);

    if ($employee_id < 1 || $year < 2000 || $target_value < 0) {
        echo json_encode(['success' => false, 'msg' => 'بيانات غير صحيحة']);
        exit;
    }
    if (!in_array($period_type, ['monthly','yearly'], true)) {
        $period_type = 'monthly';
    }
    if ($period_type === 'yearly') {
        $month = null;
    } elseif ($month < 1 || $month > 12) {
        echo json_encode(['success' => false, 'msg' => 'الشهر غير صحيح']);
        exit;
    }

    $notes_esc = $conn->real_escape_string($notes);

    if ($month === null) {
        $sql = "INSERT INTO employee_targets (employee_id, period_type, year, month, target_value, notes, user)
                VALUES ($employee_id, 'yearly', $year, NULL, $target_value, '$notes_esc', $user)
                ON DUPLICATE KEY UPDATE target_value=$target_value, notes='$notes_esc', user=$user";
    } else {
        $sql = "INSERT INTO employee_targets (employee_id, period_type, year, month, target_value, notes, user)
                VALUES ($employee_id, 'monthly', $year, $month, $target_value, '$notes_esc', $user)
                ON DUPLICATE KEY UPDATE target_value=$target_value, notes='$notes_esc', user=$user";
    }

    $conn->query($sql);
    echo json_encode(['success' => true, 'msg' => 'تم الحفظ بنجاح']);
    exit;
}

/* ─────────────────────────────────────────
   LOAD  (GET)
───────────────────────────────────────── */
if ($action === 'load') {
    $employee_id = (int) ($_GET['employee_id'] ?? 0);
    $year        = (int) ($_GET['year']        ?? date('Y'));

    if ($employee_id < 1) {
        echo json_encode(['success' => false, 'msg' => 'موظف غير محدد']);
        exit;
    }

    $res = $conn->query("
        SELECT period_type, year, month, target_value, notes
        FROM employee_targets
        WHERE employee_id = $employee_id AND year = $year
        ORDER BY period_type DESC, month ASC
    ");

    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }

    echo json_encode(['success' => true, 'data' => $rows]);
    exit;
}

/* ─────────────────────────────────────────
   DELETE (POST)
───────────────────────────────────────── */
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $conn->query("DELETE FROM employee_targets WHERE id = $id");
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'msg' => 'معرف غير صحيح']);
    }
    exit;
}

echo json_encode(['success' => false, 'msg' => 'طلب غير معروف']);
