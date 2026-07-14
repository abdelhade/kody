<?php
/**
 * get_audit_history.php
 * AJAX handler — returns audit log records filtered by date range
 */

session_start();

header('Content-Type: application/json; charset=utf-8');

// Auth check
if (!isset($_SESSION['login'])) {
    echo json_encode(['success' => false, 'message' => 'غير مصرح — يرجى تسجيل الدخول']);
    exit;
}

require_once __DIR__ . '/../includes/connect.php';

// Check if table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'inventory_audit_log'");
if ($tableCheck->num_rows === 0) {
    echo json_encode(['success' => true, 'sessions' => [], 'total' => 0]);
    exit;
}

// Get filter params
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$dateTo   = isset($_GET['date_to'])   ? $_GET['date_to']   : '';
$session  = isset($_GET['session'])   ? $_GET['session']    : '';

// Build query for sessions overview
$where = [];
$params = [];
$types  = '';

if (!empty($dateFrom)) {
    $where[]  = 'created_at >= ?';
    $params[] = $dateFrom . ' 00:00:00';
    $types   .= 's';
}
if (!empty($dateTo)) {
    $where[]  = 'created_at <= ?';
    $params[] = $dateTo . ' 23:59:59';
    $types   .= 's';
}
if (!empty($session)) {
    $where[]  = 'audit_session = ?';
    $params[] = $session;
    $types   .= 's';
}

$whereClause = '';
if (!empty($where)) {
    $whereClause = 'WHERE ' . implode(' AND ', $where);
}

// Get audit sessions grouped
$sql = "
    SELECT 
        audit_session,
        MIN(created_at) AS session_date,
        user_name,
        COUNT(*) AS items_count,
        SUM(CASE WHEN qty_diff > 0 THEN qty_diff ELSE 0 END) AS total_surplus,
        SUM(CASE WHEN qty_diff < 0 THEN ABS(qty_diff) ELSE 0 END) AS total_deficit,
        SUM(CASE WHEN qty_diff = 0 THEN 1 ELSE 0 END) AS match_count,
        SUM(CASE WHEN qty_diff > 0 THEN 1 ELSE 0 END) AS surplus_count,
        SUM(CASE WHEN qty_diff < 0 THEN 1 ELSE 0 END) AS deficit_count
    FROM inventory_audit_log
    $whereClause
    GROUP BY audit_session
    ORDER BY session_date DESC
    LIMIT 50
";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$sessions = [];
while ($row = $result->fetch_assoc()) {
    $sessions[] = $row;
}
$stmt->close();

// If a specific session is requested, also get detailed items
$details = [];
if (!empty($session)) {
    $sqlDetails = "
        SELECT id, item_id, item_name, item_barcode, qty_before, qty_actual, qty_diff, user_name, notes, created_at
        FROM inventory_audit_log
        WHERE audit_session = ?
        ORDER BY item_name
    ";
    $stmtD = $conn->prepare($sqlDetails);
    $stmtD->bind_param('s', $session);
    $stmtD->execute();
    $resD = $stmtD->get_result();
    while ($row = $resD->fetch_assoc()) {
        $details[] = $row;
    }
    $stmtD->close();
}

echo json_encode([
    'success'  => true,
    'sessions' => $sessions,
    'details'  => $details,
    'total'    => count($sessions)
]);
