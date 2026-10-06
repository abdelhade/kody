<?php
include('../includes/connect.php');

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'error' => 'غير مصرح']);
    exit;
}

$accountId = (int) ($_GET['id'] ?? 0);
$excludeOp = (int) ($_GET['exclude_op'] ?? 0);

if ($accountId <= 0) {
    echo json_encode(['success' => false, 'error' => 'حساب غير صحيح']);
    exit;
}

// عند التعديل نستبعد قيود الفاتورة نفسها (وسنداتها) عشان "قبل" يكون قبل الفاتورة
if ($excludeOp > 0) {
    $stmt = $conn->prepare(
        'SELECT COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) AS bal
         FROM journal_entries
         WHERE account_id = ? AND isdeleted = 0
           AND NOT (COALESCE(op2, 0) = ?
                OR journal_id IN (SELECT id FROM journal_heads WHERE op_id = ? OR op2 = ?))'
    );
    $stmt->bind_param('iiii', $accountId, $excludeOp, $excludeOp, $excludeOp);
} else {
    $stmt = $conn->prepare(
        'SELECT COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) AS bal
         FROM journal_entries
         WHERE account_id = ? AND isdeleted = 0'
    );
    $stmt->bind_param('i', $accountId);
}

if (!$stmt || !$stmt->execute()) {
    echo json_encode(['success' => false, 'error' => 'خطأ في الاستعلام']);
    exit;
}
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

echo json_encode(['success' => true, 'balance' => round((float) ($row['bal'] ?? 0), 2)]);
