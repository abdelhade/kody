<?php
include '../includes/connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_GET['id'])) {
    header('Location: ../shift_groups.php');
    exit;
}

$groupId = (int) $_GET['id'];
$name = trim($_POST['name'] ?? '');
$info = trim($_POST['info'] ?? '');
$max_detect_window = max(30, min(1440, (int) ($_POST['max_detect_window'] ?? 720)));
$shiftIds = isset($_POST['shifts']) && is_array($_POST['shifts']) ? array_map('intval', $_POST['shifts']) : [];

if ($groupId <= 0 || $name === '') {
    header("Location: ../edit_shift_group.php?id=$groupId&error=empty_name");
    exit;
}

if (empty($shiftIds)) {
    header("Location: ../edit_shift_group.php?id=$groupId&error=no_shifts");
    exit;
}

$stmt = $conn->prepare("UPDATE shift_groups SET name = ?, info = ?, max_detect_window = ? WHERE id = ?");
$stmt->bind_param('ssii', $name, $info, $max_detect_window, $groupId);
if (!$stmt->execute()) {
    $stmt->close();
    header("Location: ../edit_shift_group.php?id=$groupId&error=save_failed");
    exit;
}
$stmt->close();

// إعادة تعيين الورديات في المجموعة
$stmtDel = $conn->prepare("DELETE FROM shift_group_items WHERE group_id = ?");
$stmtDel->bind_param('i', $groupId);
$stmtDel->execute();
$stmtDel->close();

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

$conn->query("INSERT INTO `process`(`type`) VALUES ('edit shift group')");

header('Location: ../shift_groups.php?success=updated');
exit;
