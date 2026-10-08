<?php
include('../includes/connect.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../deleted_items.php');
    exit;
}

$action = trim((string) ($_POST['bulk_action'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$srvrpass = $rowstg['edit_pass'] ?? '';
$search = trim((string) ($_POST['search'] ?? ''));
$page = max(1, (int) ($_POST['page'] ?? 1));

$redirectQuery = [];
if ($search !== '') {
    $redirectQuery['search'] = $search;
}
if ($page > 1) {
    $redirectQuery['page'] = $page;
}

function buildRedirectUrl(array $baseQuery, array $extra): string {
    $merged = array_merge($baseQuery, $extra);
    return '../deleted_items.php' . (!empty($merged) ? '?' . http_build_query($merged) : '');
}

if ($password !== $srvrpass) {
    header('Location: ' . buildRedirectUrl($redirectQuery, ['pass' => 0]));
    exit;
}

$item_ids = isset($_POST['item_ids']) ? array_filter(array_map('intval', (array) $_POST['item_ids'])) : [];
$item_ids = array_unique(array_filter($item_ids, function($id) { return $id > 0; }));

if (empty($item_ids)) {
    header('Location: ' . buildRedirectUrl($redirectQuery, ['no_selection' => 1]));
    exit;
}

if ($action === 'restore') {
    $in = implode(',', array_fill(0, count($item_ids), '?'));
    $types = str_repeat('i', count($item_ids));
    $stmt = $conn->prepare("UPDATE myitems SET isdeleted = 0 WHERE id IN ($in) AND isdeleted = 1");
    if ($stmt) {
        $stmt->bind_param($types, ...$item_ids);
        $stmt->execute();
        $restoredCount = $stmt->affected_rows;
        $stmt->close();
        header('Location: ' . buildRedirectUrl($redirectQuery, ['bulk_restored' => max(0, $restoredCount)]));
        exit;
    }
    header('Location: ' . buildRedirectUrl($redirectQuery, ['restore_error' => 'failed']));
    exit;
}

if ($action === 'purge') {
    $in = implode(',', array_fill(0, count($item_ids), '?'));
    $types = str_repeat('i', count($item_ids));
    
    // فحص الأصناف التي عليها حركات فواتير
    $moved_ids = [];
    $stmtMoves = $conn->prepare("SELECT DISTINCT item_id FROM fat_details WHERE item_id IN ($in) AND IFNULL(isdeleted, 0) = 0");
    if ($stmtMoves) {
        $stmtMoves->bind_param($types, ...$item_ids);
        $stmtMoves->execute();
        $resMoves = $stmtMoves->get_result();
        while ($row = $resMoves->fetch_assoc()) {
            $moved_ids[] = (int) $row['item_id'];
        }
        $stmtMoves->close();
    }

    $purge_ids = array_values(array_diff($item_ids, $moved_ids));
    $skipped_count = count($moved_ids);

    if (empty($purge_ids)) {
        header('Location: ' . buildRedirectUrl($redirectQuery, ['purge_error' => 'all_moves', 'skipped' => $skipped_count]));
        exit;
    }

    $conn->begin_transaction();
    try {
        $inPurge = implode(',', array_fill(0, count($purge_ids), '?'));
        $typesPurge = str_repeat('i', count($purge_ids));

        $queries = [
            "DELETE FROM barcodes WHERE item_id IN ($inPurge)",
            "DELETE FROM item_units WHERE item_id IN ($inPurge)",
            "DELETE FROM imgs WHERE itemid IN ($inPurge)",
            "DELETE FROM myitems WHERE id IN ($inPurge) AND isdeleted = 1",
        ];

        foreach ($queries as $sql) {
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception($conn->error);
            }
            $stmt->bind_param($typesPurge, ...$purge_ids);
            if (!$stmt->execute()) {
                throw new Exception($stmt->error);
            }
            $stmt->close();
        }

        $conn->commit();
        header('Location: ' . buildRedirectUrl($redirectQuery, [
            'bulk_purged' => count($purge_ids),
            'skipped' => $skipped_count
        ]));
        exit;
    } catch (Throwable $e) {
        $conn->rollback();
        header('Location: ' . buildRedirectUrl($redirectQuery, ['purge_error' => 'failed']));
        exit;
    }
}

header('Location: ' . buildRedirectUrl($redirectQuery, []));
exit;
