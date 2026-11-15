<?php
/**
 * واجهة وكيل الطباعة المحلية (Tauri).
 * action=ping|groups|jobs
 *
 * ping/groups: عامة للجهاز المحلي
 * jobs: تحتاج تسجيل دخول (من المتصفح) لبناء روابط بتوكن
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$action = isset($_GET['action']) ? (string) $_GET['action'] : 'ping';

if ($action === 'ping') {
    // اتصال خفيف بدون قاعدة إن أمكن، ومع قاعدة لو موجودة
    $okDb = false;
    try {
        require_once __DIR__ . '/../includes/connect.php';
        $okDb = isset($conn) && $conn instanceof mysqli;
    } catch (Throwable $e) {
        $okDb = false;
    }

    echo json_encode([
        'ok' => true,
        'service' => 'kody',
        'db' => $okDb,
        'message' => $okDb ? 'كودي جاهز' : 'كودي رد لكن قاعدة البيانات غير متاحة',
        'time' => date('c'),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once __DIR__ . '/../includes/connect.php';
require_once __DIR__ . '/../classes/PrintAgentToken.php';

if ($action === 'groups') {
    $groups = [];
    $res = @$conn->query("SELECT id, gname FROM item_group WHERE isdeleted = 0 ORDER BY gname");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $groups[] = [
                'id' => (int) $row['id'],
                'name' => (string) $row['gname'],
            ];
        }
    }
    echo json_encode([
        'ok' => true,
        'groups' => $groups,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === 'jobs') {
    require_once __DIR__ . '/../classes/Can.php';
    if (!Can::isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['ok' => false, 'message' => 'يجب تسجيل الدخول'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if (!Can::any('pos.use', 'sales.view', 'pos.closed_sessions')) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'message' => 'غير مصرح'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $mode = isset($_GET['mode']) ? (string) $_GET['mode'] : 'both';
    // receipt | prep | both
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'message' => 'رقم فاتورة غير صالح'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // مسار المشروع من موقع api/
    $basePath = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/kody')), '/\\');
    if ($basePath === '' || $basePath === '\\') {
        $basePath = '';
    }
    $origin = $scheme . '://' . $host . $basePath;

    $jobs = [];

    if ($mode === 'receipt' || $mode === 'both') {
        $q = PrintAgentToken::query($id, 'print');
        $jobs[] = [
            'type' => 'receipt',
            'url' => $origin . '/print/receipt.php?id=' . $id . '&' . $q . '&agent_print=1',
        ];
    }

    if ($mode === 'prep' || $mode === 'both') {
        $groupIds = [];
        $sql = "SELECT DISTINCT COALESCE(NULLIF(i.group1, 0), 0) AS gid
                FROM fat_details fd
                LEFT JOIN myitems i ON i.id = fd.item_id
                WHERE fd.isdeleted = 0 AND (fd.fatid = ? OR fd.pro_id = ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ii', $id, $id);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($res && ($row = $res->fetch_assoc())) {
                $gid = (int) ($row['gid'] ?? 0);
                if ($gid > 0) {
                    $groupIds[$gid] = true;
                } else {
                    $groupIds[0] = true;
                }
            }
            $stmt->close();
        }

        if (empty($groupIds)) {
            $q = PrintAgentToken::query($id, 'prep');
            $jobs[] = [
                'type' => 'prep',
                'group_id' => null,
                'url' => $origin . '/print/preparation.php?id=' . $id . '&' . $q . '&agent_print=1',
            ];
        } else {
            foreach (array_keys($groupIds) as $gid) {
                $q = PrintAgentToken::query($id, 'prep');
                $gid = (int) $gid;
                $url = $origin . '/print/preparation.php?id=' . $id
                    . '&' . $q . '&agent_print=1&group=' . $gid;
                $jobs[] = [
                    'type' => 'prep',
                    'group_id' => $gid > 0 ? $gid : null,
                    'url' => $url,
                ];
            }
        }
    }

    echo json_encode([
        'ok' => true,
        'id' => $id,
        'mode' => $mode,
        'jobs' => $jobs,
        'agent' => 'http://127.0.0.1:17890/print',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(400);
echo json_encode(['ok' => false, 'message' => 'action غير معروف'], JSON_UNESCAPED_UNICODE);
