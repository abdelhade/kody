<?php
/**
 * مدخل قديم للطباعة:
 * - مباشرة: تحويل فوري للـ POS مع print_id (من غير صفحة «تم الإرسال للطابور»)
 * - غير مباشرة: combo
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}
require_once __DIR__ . '/../includes/connect.php';
require_once __DIR__ . '/../includes/receipt_design.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    echo 'لا يوجد فاتورة بهذا الرقم';
    exit;
}

$stmt = $conn->prepare('SELECT id FROM ot_head WHERE id = ? AND isdeleted = 0 LIMIT 1');
$rowfat = null;
if ($stmt) {
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $rowfat = $res ? $res->fetch_assoc() : null;
    $stmt->close();
}
if (!$rowfat) {
    echo 'لا يوجد فاتورة بهذا الرقم';
    exit;
}

$design = kody_receipt_design_load($rowstg ?? []);
$direct = !empty($design['direct_print']);

if (!$direct) {
    header('Location: combo.php?id=' . $id);
    exit;
}

$posPage = '../pos_barcode.php';
$posType = $rowstg['pos_type'] ?? 'barcode';
if ($posType === 'clothes') {
    $posPage = '../pos_clothes.php';
} elseif ($posType === 'tables' || $posType === 'restaurant') {
    $posPage = '../pos_tables.php';
}
if (!empty($_SESSION['pos_back_page']) && preg_match('~/pos_[a-z0-9_]+\.php~i', (string) $_SESSION['pos_back_page'], $m)) {
    $posPage = '..' . $m[0];
}

header('Location: ' . $posPage . '?print_id=' . $id . '&r=' . time());
exit;
