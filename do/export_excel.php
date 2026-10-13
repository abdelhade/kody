<?php
/**
 * Backend Excel Export Endpoint (do/export_excel.php)
 * Streams database tables and reports directly as styled RTL XLSX files.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login'])) {
    http_response_code(403);
    die('غير مصرح بالدخول');
}

require_once __DIR__ . '/../includes/connect.php';
require_once __DIR__ . '/../classes/KodyExcelExporter.php';

$type = $_GET['type'] ?? $_POST['type'] ?? '';
$from = $_GET['from'] ?? $_POST['from'] ?? '';
$to = $_GET['to'] ?? $_POST['to'] ?? '';

// Fetch company name from settings
$companyName = 'النظام';
$stgRes = $conn->query("SELECT company_name FROM settings LIMIT 1");
if ($stgRes && $rowStg = $stgRes->fetch_assoc()) {
    $companyName = $rowStg['company_name'] ?? 'النظام';
}

switch ($type) {
    case 'clients':
        $sql = "SELECT id, cname, cphone, cphone2, caddress, balance, notes FROM clients WHERE isdeleted = 0 ORDER BY id DESC";
        $headerMap = [
            'id' => 'كود العميل',
            'cname' => 'اسم العميل',
            'cphone' => 'الهاتف الأساسي',
            'cphone2' => 'الهاتف البديل',
            'caddress' => 'العنوان',
            'balance' => 'الرصيد الحالي',
            'notes' => 'الملاحظات'
        ];
        KodyExcelExporter::downloadQuery($conn, $sql, 'تقرير_العملاء', $headerMap, [
            'title' => 'قائمة العملاء وأرصدتهم',
            'company' => $companyName,
            'date' => 'تاريخ التصدير: ' . date('Y-m-d H:i')
        ]);
        break;

    case 'items':
    case 'myitems':
        $search = trim((string)($_GET['search'] ?? $_POST['search'] ?? ''));
        $g1 = (int)($_GET['group1'] ?? $_POST['group1'] ?? 0);
        $g2 = (int)($_GET['group2'] ?? $_POST['group2'] ?? 0);
        $all = (int)($_GET['all'] ?? $_POST['all'] ?? 0);

        $where = "WHERE i.isdeleted = 0";
        if (!$all) {
            if ($search !== '') {
                $kwEsc = $conn->real_escape_string($search);
                $keywords = preg_split('/\s+/', $kwEsc);
                foreach ($keywords as $kw) {
                    $kw = trim($kw);
                    if ($kw !== '') {
                        $where .= " AND (i.iname LIKE '%$kw%' OR i.barcode LIKE '%$kw%' OR i.code LIKE '%$kw%' OR i.info LIKE '%$kw%')";
                    }
                }
            }
            if ($g1 > 0) {
                $where .= " AND i.group1 = $g1";
            }
            if ($g2 > 0) {
                $where .= " AND i.group2 = $g2";
            }
        }

        $priceListNames = [1 => 'سعر 1 (قطاعي)', 2 => 'سعر 2 (جملة)', 3 => 'سعر 3'];
        if (file_exists(__DIR__ . '/../classes/InvoiceProcessor.php')) {
            require_once __DIR__ . '/../classes/InvoiceProcessor.php';
            foreach (InvoiceProcessor::priceLists($conn) as $plist) {
                $plId = (int) ($plist['id'] ?? 0);
                if ($plId >= 1 && $plId <= 3 && trim((string) $plist['pname']) !== '') {
                    $priceListNames[$plId] = (string) $plist['pname'];
                }
            }
        }

        $sql = "SELECT 
                    i.id,
                    COALESCE(i.barcode, '') AS barcode,
                    COALESCE(i.code, '') AS code,
                    i.iname,
                    COALESCE(g1.gname, '') AS group1_name,
                    COALESCE(g2.gname, '') AS group2_name,
                    COALESCE(u.uname, '') AS unit_name,
                    i.itmqty,
                    COALESCE(i.info, '') AS info,
                    i.price1,
                    i.price2,
                    CASE WHEN i.price3 > 0 THEN i.price3 ELSE COALESCE(i.market_price, 0) END AS price3_val,
                    i.last_price,
                    i.cost_price
                FROM myitems i 
                LEFT JOIN item_group g1 ON i.group1 = g1.id 
                LEFT JOIN item_group2 g2 ON i.group2 = g2.id 
                LEFT JOIN item_units iu ON (iu.item_id = i.id AND iu.isdeleted = 0 AND (iu.def_stock = 1 OR iu.def_sale = 1))
                LEFT JOIN myunits u ON iu.unit_id = u.id
                $where 
                GROUP BY i.id
                ORDER BY i.id DESC";

        $res = $conn->query($sql);
        if (!$res) {
            http_response_code(500);
            die('خطأ في الاستعلام: ' . $conn->error);
        }

        $headers = [
            'م',
            'رقم الصنف',
            'الباركود',
            'كود الصنف',
            'الاسم',
            'الكمية',
            'الوحدة',
            'المجموعة',
            'التصنيف',
            'الوصف',
            $priceListNames[1],
            $priceListNames[2],
            $priceListNames[3],
            'سعر الشراء',
            'سعر التكلفة'
        ];

        $rows = [];
        $seq = 1;
        while ($r = $res->fetch_assoc()) {
            $rows[] = [
                $seq++,
                (int)$r['id'],
                (string)$r['barcode'],
                (string)$r['code'],
                (string)$r['iname'],
                (float)$r['itmqty'],
                (string)$r['unit_name'],
                (string)$r['group1_name'],
                (string)$r['group2_name'],
                (string)$r['info'],
                (float)$r['price1'],
                (float)$r['price2'],
                (float)$r['price3_val'],
                (float)$r['last_price'],
                (float)$r['cost_price']
            ];
        }

        $filterNote = ($all || ($search === '' && $g1 === 0 && $g2 === 0)) ? 'كامل دليل الأصناف (بدون حدود)' : 'أصناف محددة بالبحث (بدون حدود)';

        KodyExcelExporter::download('دليل_الأصناف.xlsx', $headers, $rows, [
            'title' => 'دليل الأصناف والأسعار (SQL) - ' . $filterNote,
            'company' => $companyName,
            'date' => 'تاريخ التصدير: ' . date('Y-m-d H:i')
        ]);
        break;

    case 'employees':
        $sql = "SELECT id, ename, ephone, national_id, salary, hire_date FROM employees WHERE isdeleted = 0 ORDER BY id DESC";
        $headerMap = [
            'id' => 'كود الموظف',
            'ename' => 'اسم الموظف',
            'ephone' => 'رقم الهاتف',
            'national_id' => 'الرقم القومي',
            'salary' => 'الراتب الأساسي',
            'hire_date' => 'تاريخ التعيين'
        ];
        KodyExcelExporter::downloadQuery($conn, $sql, 'تقرير_الموظفين', $headerMap, [
            'title' => 'بيانات الموظفين والرواتب',
            'company' => $companyName,
            'date' => 'تاريخ التصدير: ' . date('Y-m-d H:i')
        ]);
        break;

    case 'sales':
        $where = "WHERE (h.pro_tybe = 9 OR h.pro_tybe = 3) AND h.isdeleted = 0";
        if ($from && $to) {
            $where .= " AND h.pro_date BETWEEN '$from' AND '$to'";
        }
        $sql = "SELECT h.id, h.pro_date, h.pro_time, h.pro_value, h.paid, (h.pro_value - h.paid) as remain, c.cname, u.name as cashier_name
                FROM ot_head h
                LEFT JOIN clients c ON h.client_id = c.id
                LEFT JOIN users u ON h.user_id = u.id
                $where
                ORDER BY h.id DESC";
        $headerMap = [
            'id' => 'رقم الفاتورة',
            'pro_date' => 'التاريخ',
            'pro_time' => 'الوقت',
            'cname' => 'العميل',
            'cashier_name' => 'الكاشير',
            'pro_value' => 'الإجمالي',
            'paid' => 'المدفوع',
            'remain' => 'المتبقي'
        ];
        KodyExcelExporter::downloadQuery($conn, $sql, 'تقرير_المبيعات', $headerMap, [
            'title' => 'تقرير فواتير المبيعات',
            'company' => $companyName,
            'date' => ($from && $to) ? "الفترة من $from إلى $to" : ('تاريخ التصدير: ' . date('Y-m-d H:i'))
        ]);
        break;

    default:
        http_response_code(400);
        die('نوع التقرير غير محدد أو غير معتمد');
}
