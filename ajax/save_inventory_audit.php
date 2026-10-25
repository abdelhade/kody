<?php
/**
 * save_inventory_audit.php
 * AJAX handler — receives counted quantities, logs the audit, and records the
 * difference as a stock movement (pro_tybe "تسوية جرد") in fat_details.
 * itmqty is then re-synced from fat_details so it never drifts from the ledger.
 */

session_start();

header('Content-Type: application/json; charset=utf-8');

// Auth check
if (!isset($_SESSION['login'])) {
    echo json_encode(['success' => false, 'message' => 'غير مصرح — يرجى تسجيل الدخول']);
    exit;
}

require_once __DIR__ . '/../includes/connect.php';

const AUDIT_PRO_NAME = 'تسوية جرد';
const QTY_EPSILON    = 0.0005;

// ─── Auto-create audit log table if it doesn't exist ───
$conn->query("
    CREATE TABLE IF NOT EXISTS inventory_audit_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        audit_session VARCHAR(50) NOT NULL,
        item_id INT NOT NULL,
        item_name VARCHAR(255),
        item_barcode VARCHAR(100),
        qty_before DECIMAL(15,2) NOT NULL,
        qty_actual DECIMAL(15,2) NOT NULL,
        qty_diff DECIMAL(15,2) NOT NULL,
        fat_detail_id INT NULL,
        user_id INT,
        user_name VARCHAR(100),
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_session (audit_session),
        INDEX idx_item (item_id),
        INDEX idx_date (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
$colFatDetail = $conn->query("SHOW COLUMNS FROM inventory_audit_log LIKE 'fat_detail_id'");
if ($colFatDetail && $colFatDetail->num_rows === 0) {
    $conn->query("ALTER TABLE inventory_audit_log ADD COLUMN fat_detail_id INT NULL AFTER qty_diff");
}

// ─── Ensure the "تسوية جرد" operation type exists ───
$stmtType = $conn->prepare("SELECT id FROM pro_tybes WHERE pname = ? LIMIT 1");
$auditProName = AUDIT_PRO_NAME;
$stmtType->bind_param('s', $auditProName);
$stmtType->execute();
$typeRow = $stmtType->get_result()->fetch_assoc();
$stmtType->close();
if ($typeRow) {
    $auditProType = (int) $typeRow['id'];
} else {
    $stmtType = $conn->prepare("INSERT INTO pro_tybes (pname, ptybe) VALUES (?, 0)");
    $stmtType->bind_param('s', $auditProName);
    $stmtType->execute();
    $auditProType = (int) $conn->insert_id;
    $stmtType->close();
    $conn->query("UPDATE pro_tybes SET ptybe = id WHERE id = $auditProType");
}

// Accept JSON body only
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (empty($data['items']) || !is_array($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'لا توجد بيانات للحفظ']);
    exit;
}

$items = $data['items'];
$notes = isset($data['notes']) ? trim($data['notes']) : '';

// Validate items
foreach ($items as $item) {
    if (!isset($item['id']) || !isset($item['qty']) || !is_numeric($item['qty'])) {
        echo json_encode(['success' => false, 'message' => 'بيانات غير صحيحة']);
        exit;
    }
    if (floatval($item['qty']) < 0) {
        echo json_encode(['success' => false, 'message' => 'لا يمكن أن تكون الكمية الفعلية سالبة']);
        exit;
    }
}

// Generate audit session ID (date-based short UUID)
$auditSession = 'AUD-' . date('Ymd-His') . '-' . substr(md5(uniqid(mt_rand(), true)), 0, 6);

// Get user info
$userId   = isset($_SESSION['userid']) ? intval($_SESSION['userid']) : 0;
$userName = isset($_SESSION['login'])  ? $_SESSION['login'] : 'غير معروف';
$storeId  = isset($data['store_id']) ? (int) $data['store_id'] : 0;
if ($storeId <= 0) {
    $storeId = (int) (getUserDefault('def_store', $conn) ?: 0);
}
if ($storeId <= 0) {
    echo json_encode(['success' => false, 'message' => 'يجب اختيار المخزن']);
    exit;
}
$stmtStoreOk = $conn->prepare('SELECT id FROM acc_head WHERE id = ? AND is_stock = 1 AND isdeleted = 0 LIMIT 1');
$stmtStoreOk->bind_param('i', $storeId);
$stmtStoreOk->execute();
if (!$stmtStoreOk->get_result()->fetch_assoc()) {
    $stmtStoreOk->close();
    echo json_encode(['success' => false, 'message' => 'المخزن غير صالح']);
    exit;
}
$stmtStoreOk->close();

$conn->begin_transaction();
$errors = [];
$results = [];

try {
    // Prepare statements
    $stmtSelect = $conn->prepare("SELECT id, iname, barcode, cost_price FROM myitems WHERE id = ? AND isdeleted = 0 FOR UPDATE");
    $stmtLedger = $conn->prepare("
        SELECT COALESCE(SUM(qty_in), 0) - COALESCE(SUM(qty_out), 0) AS qty
        FROM fat_details
        WHERE item_id = ? AND det_store = ? AND isdeleted = 0
    ");
    $stmtMove = $conn->prepare("
        INSERT INTO fat_details
            (pro_tybe, fat_tybe, det_store, item_id, u_val, qty_in, qty_out, doc_qty, price, cost_price, det_value, profit)
        VALUES (?, ?, ?, ?, 1, ?, ?, ?, ?, ?, ?, 0)
    ");
    $stmtLog = $conn->prepare("
        INSERT INTO inventory_audit_log 
            (audit_session, item_id, item_name, item_barcode, qty_before, qty_actual, qty_diff, fat_detail_id, user_id, user_name, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmtSync = $conn->prepare("
        UPDATE myitems
        SET itmqty = (
            SELECT COALESCE(SUM(qty_in), 0) - COALESCE(SUM(qty_out), 0)
            FROM fat_details
            WHERE item_id = myitems.id AND isdeleted = 0
        )
        WHERE id = ?
    ");

    if (!$stmtSelect || !$stmtLedger || !$stmtMove || !$stmtLog || !$stmtSync) {
        throw new Exception('فشل تحضير الاستعلام: ' . $conn->error);
    }

    foreach ($items as $item) {
        $id  = intval($item['id']);
        $qty = round(floatval($item['qty']), 3);

        if ($id < 1) {
            $errors[] = "معرف صنف غير صالح: $id";
            continue;
        }

        // 1. Lock and read the item
        $stmtSelect->bind_param('i', $id);
        $stmtSelect->execute();
        $row = $stmtSelect->get_result()->fetch_assoc();

        if (!$row) {
            $errors[] = "صنف غير موجود: $id";
            continue;
        }

        // 2. رصيد المخزن المختار من دفتر الحركة
        $stmtLedger->bind_param('ii', $id, $storeId);
        $stmtLedger->execute();
        $qtyBefore = round((float) $stmtLedger->get_result()->fetch_assoc()['qty'], 3);

        $qtyDiff = round($qty - $qtyBefore, 3);
        if (abs($qtyDiff) < QTY_EPSILON) {
            $qtyDiff = 0.0;
        }

        // 3. Record the difference as a stock movement
        $fatDetailId = null;
        if ($qtyDiff != 0) {
            $qtyIn    = $qtyDiff > 0 ? $qtyDiff : 0.0;
            $qtyOut   = $qtyDiff < 0 ? -$qtyDiff : 0.0;
            $docQty   = abs($qtyDiff);
            $cost     = (float) $row['cost_price'];
            $detValue = round($docQty * $cost, 2);

            $stmtMove->bind_param(
                'iiiidddddd',
                $auditProType,
                $auditProType,
                $storeId,
                $id,
                $qtyIn,
                $qtyOut,
                $docQty,
                $cost,
                $cost,
                $detValue
            );
            $stmtMove->execute();
            $fatDetailId = (int) $conn->insert_id;

            $stmtSync->bind_param('i', $id);
            $stmtSync->execute();
        }

        // 4. Log the audit line (matching items too, so the session is a full count record)
        $itemName = $row['iname'];
        $barcode  = $row['barcode'] ?? '';
        $stmtLog->bind_param(
            'sissdddiiss',
            $auditSession,
            $id,
            $itemName,
            $barcode,
            $qtyBefore,
            $qty,
            $qtyDiff,
            $fatDetailId,
            $userId,
            $userName,
            $notes
        );
        $stmtLog->execute();

        $results[] = ['id' => $id, 'qty_before' => $qtyBefore, 'qty' => $qty, 'diff' => $qtyDiff];
    }

    $stmtSelect->close();
    $stmtLedger->close();
    $stmtMove->close();
    $stmtLog->close();
    $stmtSync->close();

    if (!empty($errors)) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => implode(' | ', $errors)]);
        exit;
    }

    $conn->commit();
    echo json_encode([
        'success'       => true,
        'message'       => 'تم حفظ جرد ' . count($results) . ' صنف بنجاح',
        'count'         => count($results),
        'items'         => $results,
        'audit_session' => $auditSession
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
