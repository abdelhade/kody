<?php
/**
 * save_inventory_audit.php
 * AJAX handler — receives actual quantities, logs the audit, then updates myitems.itmqty
 */

session_start();

header('Content-Type: application/json; charset=utf-8');

// Auth check
if (!isset($_SESSION['login'])) {
    echo json_encode(['success' => false, 'message' => 'غير مصرح — يرجى تسجيل الدخول']);
    exit;
}

require_once __DIR__ . '/../includes/connect.php';

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
        user_id INT,
        user_name VARCHAR(100),
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_session (audit_session),
        INDEX idx_item (item_id),
        INDEX idx_date (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

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
    if (!isset($item['id']) || !isset($item['qty'])) {
        echo json_encode(['success' => false, 'message' => 'بيانات غير صحيحة']);
        exit;
    }
}

// Generate audit session ID (date-based short UUID)
$auditSession = 'AUD-' . date('Ymd-His') . '-' . substr(md5(uniqid(mt_rand(), true)), 0, 6);

// Get user info
$userId   = isset($_SESSION['userid']) ? intval($_SESSION['userid']) : 0;
$userName = isset($_SESSION['login'])  ? $_SESSION['login'] : 'غير معروف';

$conn->begin_transaction();
$errors = [];

try {
    // Prepare statements
    $stmtSelect = $conn->prepare("SELECT id, iname, barcode, itmqty FROM myitems WHERE id = ? AND isdeleted = 0");
    $stmtLog    = $conn->prepare("
        INSERT INTO inventory_audit_log 
            (audit_session, item_id, item_name, item_barcode, qty_before, qty_actual, qty_diff, user_id, user_name, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmtUpdate = $conn->prepare("UPDATE myitems SET itmqty = ? WHERE id = ? AND isdeleted = 0");

    if (!$stmtSelect || !$stmtLog || !$stmtUpdate) {
        throw new Exception('فشل تحضير الاستعلام: ' . $conn->error);
    }

    foreach ($items as $item) {
        $id  = intval($item['id']);
        $qty = floatval($item['qty']);

        if ($id < 1) {
            $errors[] = "معرف صنف غير صالح: $id";
            continue;
        }

        // 1. Read current item data
        $stmtSelect->bind_param('i', $id);
        $stmtSelect->execute();
        $result = $stmtSelect->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            $errors[] = "صنف غير موجود: $id";
            continue;
        }

        $qtyBefore = floatval($row['itmqty']);
        $qtyDiff   = $qty - $qtyBefore;
        $itemName  = $row['iname'];
        $barcode   = $row['barcode'] ?? '';

        // 2. Log the audit record BEFORE updating
        $stmtLog->bind_param(
            'sissddisss',
            $auditSession,
            $id,
            $itemName,
            $barcode,
            $qtyBefore,
            $qty,
            $qtyDiff,
            $userId,
            $userName,
            $notes
        );
        if (!$stmtLog->execute()) {
            $errors[] = "خطأ في حفظ سجل الجرد للصنف رقم $id";
            continue;
        }

        // 3. Update the item quantity
        $stmtUpdate->bind_param('di', $qty, $id);
        if (!$stmtUpdate->execute()) {
            $errors[] = "خطأ في تحديث الصنف رقم $id";
        }
    }

    $stmtSelect->close();
    $stmtLog->close();
    $stmtUpdate->close();

    if (!empty($errors)) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => implode(' | ', $errors)]);
        exit;
    }

    $conn->commit();
    echo json_encode([
        'success'       => true,
        'message'       => 'تمت تسوية ' . count($items) . ' صنف بنجاح',
        'count'         => count($items),
        'audit_session' => $auditSession
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
