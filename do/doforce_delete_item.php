<?php
include('../includes/connect.php');

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$password = isset($_POST['password']) ? (string) $_POST['password'] : '';
$srvrpass = $rowstg['edit_pass'] ?? '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $id <= 0 || $password != $srvrpass) {
    header('Location: ../deleted_items.php?pass=0');
    exit;
}

$stmt = $conn->prepare('SELECT id FROM myitems WHERE id = ? AND isdeleted = 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$exists = $stmt->get_result()->num_rows > 0;
$stmt->close();

if (!$exists) {
    header('Location: ../deleted_items.php?purge_error=notfound');
    exit;
}

$stmt = $conn->prepare('SELECT COUNT(*) AS total FROM fat_details WHERE item_id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$moves = (int) $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

if ($moves > 0) {
    header('Location: ../deleted_items.php?purge_error=moves');
    exit;
}

$conn->begin_transaction();
try {
    $queries = [
        'DELETE FROM barcodes WHERE item_id = ?',
        'DELETE FROM item_units WHERE item_id = ?',
        'DELETE FROM imgs WHERE itemid = ?',
        'DELETE FROM myitems WHERE id = ? AND isdeleted = 1',
    ];
    foreach ($queries as $sql) {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception($conn->error);
        }
        $stmt->bind_param('i', $id);
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        $stmt->close();
    }
    $conn->commit();
} catch (Throwable $e) {
    $conn->rollback();
    header('Location: ../deleted_items.php?purge_error=failed');
    exit;
}

header('Location: ../deleted_items.php?purged=1');
exit;
