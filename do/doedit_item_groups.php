<?php
include('../includes/connect.php');

if (!isset($_SESSION['login'], $_SESSION['userid'])) {
    header('Location: ../index.php');
    exit;
}

$search = trim((string) ($_POST['search'] ?? ''));
$filterGroup1 = (int) ($_POST['filter_group1'] ?? 0);
$filterGroup2 = (int) ($_POST['filter_group2'] ?? 0);
$page = (int) ($_POST['page'] ?? 1);
if ($page < 1) {
    $page = 1;
}

$back = static function (array $extra) use ($search, $filterGroup1, $filterGroup2, $page): void {
    $query = array_merge([
        'search' => $search,
        'group1' => $filterGroup1,
        'group2' => $filterGroup2,
        'page' => $page,
    ], $extra);
    header('Location: ../edit_item_groups.php?' . http_build_query($query));
    exit;
};

$ids = [];
foreach ((array) ($_POST['item_ids'] ?? []) as $id) {
    $id = (int) $id;
    if ($id > 0) {
        $ids[$id] = $id;
    }
}
$ids = array_values($ids);
$newGroup = (int) ($_POST['group1'] ?? 0);
$newCategory = (int) ($_POST['group2'] ?? 0);

if ($ids === [] || $newGroup < 1) {
    $back(['error' => 'empty']);
}

$stmt = $conn->prepare('SELECT id FROM item_group WHERE id = ? AND isdeleted = 0 LIMIT 1');
$stmt->bind_param('i', $newGroup);
$stmt->execute();
$groupOk = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$groupOk) {
    $back(['error' => 'group']);
}

if ($newCategory > 0) {
    $stmt = $conn->prepare('SELECT id FROM item_group2 WHERE id = ? AND isdeleted = 0 LIMIT 1');
    $stmt->bind_param('i', $newCategory);
    $stmt->execute();
    $categoryOk = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$categoryOk) {
        $back(['error' => 'category']);
    }
}

$placeholders = implode(',', array_fill(0, count($ids), '?'));
if ($newCategory > 0) {
    $sql = "UPDATE myitems SET group1 = ?, group2 = ? WHERE isdeleted = 0 AND id IN ($placeholders)";
    $types = 'ii' . str_repeat('i', count($ids));
    $bind = array_merge([$newGroup, $newCategory], $ids);
} else {
    $sql = "UPDATE myitems SET group1 = ? WHERE isdeleted = 0 AND id IN ($placeholders)";
    $types = 'i' . str_repeat('i', count($ids));
    $bind = array_merge([$newGroup], $ids);
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    $back(['error' => 'save']);
}
$stmt->bind_param($types, ...$bind);
if (!$stmt->execute()) {
    $stmt->close();
    $back(['error' => 'save']);
}
$stmt->close();

$back(['saved' => count($ids)]);
