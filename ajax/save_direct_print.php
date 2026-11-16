<?php
/**
 * حفظ إعداد الطباعة المباشرة على قاعدة الجلسة الحالية (ديناميكية).
 * بيرد JSON دايماً — حتى عند الخطأ.
 */
header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../includes/connect.php';
    require_once __DIR__ . '/../includes/receipt_design.php';
    require_once __DIR__ . '/../classes/Can.php';

    if (!Can::isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['ok' => false, 'message' => 'يجب تسجيل الدخول'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if (!Can::any('settings.manage', 'system.manage')) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'message' => 'غير مصرح'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if (!Can::settingsUnlocked()) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'message' => 'الإعدادات مقفولة — افتحها بكلمة المرور أولاً'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $direct = isset($_POST['direct_print']) && (string) $_POST['direct_print'] === '1';

    kody_receipt_design_ensure_column($conn);
    $design = kody_receipt_design_load($rowstg ?? []);
    $design['direct_print'] = $direct;
    $json = kody_receipt_design_json($design);

    $stmt = $conn->prepare('UPDATE settings SET receipt_design = ? WHERE id = 1');
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'message' => 'فشل تحضير الاستعلام'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $stmt->bind_param('s', $json);
    $ok = $stmt->execute();
    $stmt->close();

    echo json_encode([
        'ok' => (bool) $ok,
        'direct_print' => $direct,
        'db' => isset($dbname) ? (string) $dbname : '',
        'message' => $ok
            ? ($direct ? 'تم تفعيل الطباعة المباشرة (بدون عرض)' : 'تم تفعيل الطباعة غير المباشرة')
            : 'فشل الحفظ',
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => 'خطأ في الحفظ',
    ], JSON_UNESCAPED_UNICODE);
}
