<?php
/**
 * ajax/get_operation_journals.php
 * القيود المحاسبية المرتبطة بعملية (فاتورة) — عبر op_id / op2
 */
require_once __DIR__ . '/../includes/connect.php';

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}

if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'message' => 'غير مصرح']);
    exit;
}

$opId = (int) ($_GET['op_id'] ?? $_POST['op_id'] ?? 0);
if ($opId <= 0) {
    echo json_encode(['success' => false, 'message' => 'معرف العملية غير صحيح']);
    exit;
}

// تأكيد وجود العملية
$stmt = $conn->prepare('SELECT id, pro_tybe, pro_date, fat_net, info FROM ot_head WHERE id = ? AND isdeleted != 1 LIMIT 1');
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'خطأ في الاستعلام']);
    exit;
}
$stmt->bind_param('i', $opId);
$stmt->execute();
$op = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'العملية غير موجودة']);
    exit;
}

// رؤوس القيود: مرتبطة مباشرة أو كسند على العملية
$stmt = $conn->prepare(
    'SELECT jh.id, jh.journal_id, jh.total, jh.jdate, jh.details, jh.op_id, jh.op2, jh.pro_tybe, jh.crtime,
            pt.pname AS pro_type_name
     FROM journal_heads jh
     LEFT JOIN pro_tybes pt ON pt.id = jh.pro_tybe
     WHERE jh.isdeleted = 0
       AND (COALESCE(jh.op_id, 0) = ? OR COALESCE(jh.op2, 0) = ?)
     ORDER BY jh.jdate ASC, jh.id ASC'
);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'خطأ في استعلام القيود']);
    exit;
}
$stmt->bind_param('ii', $opId, $opId);
$stmt->execute();
$heads = $stmt->get_result();
$stmt->close();

$journals = [];
$entryStmt = $conn->prepare(
    'SELECT je.id, je.account_id, je.debit, je.credit, je.tybe, je.info,
            ah.aname AS account_name, ah.code AS account_code
     FROM journal_entries je
     LEFT JOIN acc_head ah ON ah.id = je.account_id
     WHERE je.journal_id = ? AND je.isdeleted = 0
     ORDER BY je.tybe ASC, je.id ASC'
);

while ($head = $heads->fetch_assoc()) {
    $headId = (int) $head['id'];
    $entries = [];
    $sumDebit = 0.0;
    $sumCredit = 0.0;

    if ($entryStmt) {
        $entryStmt->bind_param('i', $headId);
        $entryStmt->execute();
        $entryRes = $entryStmt->get_result();
        while ($e = $entryRes->fetch_assoc()) {
            $debit = (float) $e['debit'];
            $credit = (float) $e['credit'];
            $sumDebit += $debit;
            $sumCredit += $credit;
            $entries[] = [
                'id'           => (int) $e['id'],
                'account_id'   => (int) $e['account_id'],
                'account_name' => $e['account_name'] ?: ('حساب #' . $e['account_id']),
                'account_code' => $e['account_code'] ?? '',
                'debit'        => round($debit, 2),
                'credit'       => round($credit, 2),
                'tybe'         => (int) $e['tybe'],
                'info'         => $e['info'] ?? '',
            ];
        }
    }

    $linkKind = 'main';
    if ((int) ($head['op2'] ?? 0) === $opId && (int) ($head['op_id'] ?? 0) !== $opId) {
        $linkKind = 'voucher';
    } elseif (!empty($head['pro_tybe'])) {
        $linkKind = 'typed';
    }

    $journals[] = [
        'id'            => $headId,
        'journal_num'   => (int) $head['journal_id'],
        'jdate'         => $head['jdate'],
        'details'       => $head['details'] ?? '',
        'total'         => round((float) $head['total'], 2),
        'op_id'         => (int) ($head['op_id'] ?? 0),
        'op2'           => (int) ($head['op2'] ?? 0),
        'pro_tybe'      => (int) ($head['pro_tybe'] ?? 0),
        'pro_type_name' => $head['pro_type_name'] ?? '',
        'link_kind'     => $linkKind,
        'sum_debit'     => round($sumDebit, 2),
        'sum_credit'    => round($sumCredit, 2),
        'balanced'      => abs($sumDebit - $sumCredit) < 0.01,
        'entries'       => $entries,
    ];
}
if ($entryStmt) {
    $entryStmt->close();
}

echo json_encode([
    'success'  => true,
    'op'       => [
        'id'       => (int) $op['id'],
        'pro_tybe' => (int) $op['pro_tybe'],
        'pro_date' => $op['pro_date'],
        'fat_net'  => round((float) $op['fat_net'], 2),
        'info'     => $op['info'] ?? '',
    ],
    'count'    => count($journals),
    'journals' => $journals,
], JSON_UNESCAPED_UNICODE);
