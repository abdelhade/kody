<?php
include '../includes/connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../shift_groups.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$info = trim($_POST['info'] ?? '');
$max_detect_window = max(30, min(1440, (int) ($_POST['max_detect_window'] ?? 720)));
$shiftIds = isset($_POST['shifts']) && is_array($_POST['shifts']) ? array_map('intval', $_POST['shifts']) : [];

if ($name === '') {
    header('Location: ../add_shift_group.php?error=empty_name');
    exit;
}

if (empty($shiftIds)) {
    header('Location: ../add_shift_group.php?error=no_shifts');
    exit;
}

$stmt = $conn->prepare("INSERT INTO shift_groups (name, info, max_detect_window) VALUES (?, ?, ?)");
$stmt->bind_param('ssi', $name, $info, $max_detect_window);

if (!$stmt->execute()) {
    $stmt->close();
    header('Location: ../add_shift_group.php?error=save_failed');
    exit;
}

$groupId = $conn->insert_id;
$stmt->close();

$stmtItem = $conn->prepare("INSERT INTO shift_group_items (group_id, shift_id, sort) VALUES (?, ?, ?)");
$sort = 0;
foreach ($shiftIds as $sId) {
    if ($sId > 0) {
        $sort++;
        $stmtItem->bind_param('iii', $groupId, $sId, $sort);
        $stmtItem->execute();
    }
}
$stmtItem->close();

$conn->query("INSERT INTO `process`(`type`) VALUES ('add shift group')");

header('Location: ../shift_groups.php?success=added');
exit;
