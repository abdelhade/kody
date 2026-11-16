<?php
/**
 * واجهة وكيل الطباعة المحلية (Tauri).
 * action=ping|groups|jobs
 *
 * ?db=dbname لاختيار قاعدة معيّنة (بعد التبديل / من إعدادات الوكيل)
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

require_once __DIR__ . '/../includes/load_env.php';
require_once __DIR__ . '/../includes/db_name.php';

// اختيار قاعدة من الوكيل قبل connect
$requestDb = isset($_GET['db']) ? trim((string) $_GET['db']) : '';
if ($requestDb !== '' && preg_match('/^[A-Za-z0-9_]{2,64}$/', $requestDb)) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!defined('KODY_FORCE_DBNAME')) {
        define('KODY_FORCE_DBNAME', $requestDb);
    }
    $_SESSION['active_dbname'] = $requestDb;
}

/**
 * @return list<array{name:string,label:string,is_current:bool}>
 */
function print_agent_list_databases(?string $active): array
{
    $out = [];
    $registryFile = dirname(__DIR__) . '/config/db_registry.json';
    if (!is_readable($registryFile)) {
        if ($active) {
            $out[] = ['name' => $active, 'label' => $active, 'is_current' => true];
        }
        return $out;
    }
    $reg = json_decode((string) file_get_contents($registryFile), true);
    if (!is_array($reg)) {
        return $out;
    }
    $current = (string) ($reg['current'] ?? '');
    foreach (($reg['databases'] ?? []) as $row) {
        if (!is_array($row)) {
            continue;
        }
        $name = (string) ($row['name'] ?? '');
        if ($name === '' || !preg_match('/^[A-Za-z0-9_]{2,64}$/', $name)) {
            continue;
        }
        $out[] = [
            'name' => $name,
            'label' => (string) ($row['label'] ?? $name),
            'is_current' => ($active ? $name === $active : $name === $current),
        ];
    }
    return $out;
}

require_once __DIR__ . '/../includes/connect.php';
require_once __DIR__ . '/../classes/PrintAgentToken.php';

$activeDb = isset($dbname) ? (string) $dbname : kody_preferred_dbname();
$databases = print_agent_list_databases($activeDb);

if ($action === 'ping') {
    echo json_encode([
        'ok' => true,
        'service' => 'kody',
        'db' => true,
        'dbname' => $activeDb,
        'databases' => $databases,
        'message' => 'كودي جاهز — القاعدة: ' . $activeDb,
        'time' => date('c'),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

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
        'dbname' => $activeDb,
        'databases' => $databases,
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
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'message' => 'رقم فاتورة غير صالح'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $basePath = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/kody')), '/\\');
    if ($basePath === '' || $basePath === '\\') {
        $basePath = '';
    }
    $origin = $scheme . '://' . $host . $basePath;
    $jobs = [];

    /**
     * يجيب HTML صفحة الطباعة من السيرفر (بتوكن) عشان الوكيل يطبع الملف مباشرة.
     * بيحط <base href> على مجلد print/ عشان لما الوكيل يفتح الملف محلياً
     * الصور/اللينكات النسبية متطلعش file not found.
     */
    $fetchPrintHtml = static function (string $url) use ($origin): ?string {
        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 20,
                'follow_location' => 0,
                'header' => "Accept: text/html\r\nConnection: close\r\n",
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);
        $html = @file_get_contents($url, false, $ctx);
        if ($html === false || strlen($html) < 200) {
            return null;
        }
        if (stripos($html, 'type="password"') !== false || stripos($html, "type='password'") !== false) {
            return null;
        }
        if (stripos($html, 'id="printed"') === false && stripos($html, "id='printed'") === false) {
            return null;
        }
        $baseHref = rtrim($origin, '/') . '/print/';
        // visibility:hidden + position:absolute في صفحات الطباعة أحياناً يطلّع PDF فاضي من CDP
        $inject = '<base href="' . htmlspecialchars($baseHref, ENT_QUOTES, 'UTF-8') . '">'
            . '<style id="kody-agent-thermal">@page{size:80mm auto;margin:0!important}'
            . 'html,body{width:72mm!important;max-width:72mm!important;margin:0!important;padding:0!important;background:#fff!important}'
            . '.no-print,.actions{display:none!important}'
            . '@media print{'
            . 'html,body,body *{visibility:visible!important}'
            . '#printed,#printed *{visibility:visible!important}'
            . '#printed{position:static!important;left:auto!important;top:auto!important;'
            . 'right:auto!important;width:100%!important;max-width:100%!important;margin:0!important}'
            . '.no-print,.actions{display:none!important}'
            . '}'
            . '</style>'
            . '<script id="kody-agent-no-nav">(function(){try{'
            . 'window.addEventListener("afterprint",function(e){try{e.stopImmediatePropagation();}catch(_){}},true);'
            . '}catch(_){}})();</script>';
        if (stripos($html, '</head>') !== false) {
            $html = preg_replace('/<\/head>/i', $inject . '</head>', $html, 1);
        } else {
            $html = $inject . $html;
        }
        return $html;
    };

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
            $groupIds[0] = true;
        }

        foreach (array_keys($groupIds) as $gid) {
            $gid = (int) $gid;
            $q = PrintAgentToken::query($id, 'prep', 600, $activeDb);
            $url = $origin . '/print/preparation.php?id=' . $id
                . '&' . $q . '&agent_print=1&no_auto=1';
            if ($gid > 0) {
                $url .= '&group=' . $gid;
            }
            $html = $fetchPrintHtml($url);
            if ($html === null) {
                http_response_code(502);
                echo json_encode([
                    'ok' => false,
                    'message' => 'تعذر تجهيز بون التحضير للطباعة',
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $jobs[] = [
                'type' => 'prep',
                'group_id' => $gid > 0 ? $gid : null,
                'html' => $html,
            ];
        }
    }

    if ($mode === 'receipt' || $mode === 'both') {
        $q = PrintAgentToken::query($id, 'print', 600, $activeDb);
        $url = $origin . '/print/receipt.php?id=' . $id . '&' . $q . '&agent_print=1&no_auto=1';
        $html = $fetchPrintHtml($url);
        if ($html === null) {
            http_response_code(502);
            echo json_encode([
                'ok' => false,
                'message' => 'تعذر تجهيز الفاتورة للطباعة',
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $jobs[] = [
            'type' => 'receipt',
            'html' => $html,
        ];
    }

    echo json_encode([
        'ok' => true,
        'id' => $id,
        'mode' => $mode,
        'dbname' => $activeDb,
        'jobs' => $jobs,
        'agent' => 'http://127.0.0.1:17890/print',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(400);
echo json_encode(['ok' => false, 'message' => 'action غير معروف'], JSON_UNESCAPED_UNICODE);
