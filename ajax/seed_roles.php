<?php
/**
 * ajax/seed_roles.php - معالج استدعاء زراعة الأدوار الافتراضية
 */
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['userid'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'غير مصرح (يرجى تسجيل الدخول)'], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once __DIR__ . '/../includes/connect.php';
require_once __DIR__ . '/../includes/RolesSeeder.php';

$overwriteRequested = !empty($_POST['overwrite']) && (int) $_POST['overwrite'] === 1;
if ($overwriteRequested) {
    Can::authorize('roles.edit');
}

$action = $_POST['action'] ?? $_GET['action'] ?? 'presets';

try {
    if ($action === 'presets') {
        $presets = RolesSeeder::getPresets();
        $existing = RolesSeeder::getExistingRoles($conn);

        $list = [];
        foreach ($presets as $key => $p) {
            $isExisting = isset($existing[$p['rollname']]);
            $list[] = [
                'key'         => $p['key'],
                'rollname'    => $p['rollname'],
                'info'        => $p['info'],
                'icon'        => $p['icon'],
                'category'    => $p['category'],
                'color'       => $p['color'],
                'exists'      => $isExisting,
                'existing_id' => $isExisting ? $existing[$p['rollname']] : null,
            ];
        }

        echo json_encode([
            'success' => true,
            'presets' => $list,
            'existing_count' => count($existing)
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($action === 'seed') {
        $selectedKeys = $_POST['roles'] ?? [];
        if (!is_array($selectedKeys)) {
            $selectedKeys = [];
        }

        $overwrite = !empty($_POST['overwrite']) && (int)$_POST['overwrite'] === 1;

        if (empty($selectedKeys)) {
            // إذا لم يتم تحديد أدوار معينة، نولد كل الأدوار غير الموجودة
            $selectedKeys = array_keys(RolesSeeder::getPresets());
        }

        $result = RolesSeeder::seedMany($conn, $selectedKeys, $overwrite);

        $createdCount = count($result['created']);
        $updatedCount = count($result['updated']);
        $skippedCount = count($result['skipped']);
        $errorCount   = count($result['errors']);

        $msgParts = [];
        if ($createdCount > 0) {
            $msgParts[] = "تم إنشاء $createdCount دور جديد (" . implode('، ', $result['created']) . ")";
        }
        if ($updatedCount > 0) {
            $msgParts[] = "تم تحديث $updatedCount دور (" . implode('، ', $result['updated']) . ")";
        }
        if ($skippedCount > 0) {
            $msgParts[] = "تم تخطي $skippedCount دور موجود مسبقاً (" . implode('، ', $result['skipped']) . ")";
        }
        if ($errorCount > 0) {
            $msgParts[] = "حدثت أخطاء في $errorCount دور";
        }

        $message = !empty($msgParts) ? implode(' | ', $msgParts) : 'لم يتم إجراء أي تغييرات.';

        echo json_encode([
            'success' => ($errorCount === 0),
            'message' => $message,
            'created' => $result['created'],
            'updated' => $result['updated'],
            'skipped' => $result['skipped'],
            'errors'  => $result['errors'],
            'details' => $result['details']
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'إجراء غير معروف'], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'خطأ في الخادم: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
