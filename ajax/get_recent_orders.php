<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

// Start output buffering
ob_start();

// Set content type to JSON with UTF-8
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Log request details
error_log("=== get_recent_orders.php started at " . date('Y-m-d H:i:s') . " ===");
error_log("GET: " . print_r($_GET, true));
error_log("POST: " . print_r($_POST, true));

// Start output buffering
ob_start();

// Set content type to JSON with UTF-8
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'غير مصرح بالوصول'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Include database connection
try {
    include('../includes/connect.php');
    
    // Check connection
    if ($conn->connect_error) {
        $error = 'فشل الاتصال بقاعدة البيانات: ' . $conn->connect_error;
        error_log($error);
        throw new Exception($error);
    }
    
    // Set charset to UTF-8
    $conn->set_charset('utf8');
    
    // First, check if the tables exist
    $tables = [];
    $result = $conn->query("SHOW TABLES LIKE 'ot_head'");
    $tables['ot_head'] = $result ? $result->num_rows > 0 : false;
    
    $result = $conn->query("SHOW TABLES LIKE 'clients'");
    $tables['clients'] = $result ? $result->num_rows > 0 : false;
    
    error_log("Table check - ot_head: " . ($tables['ot_head'] ? 'exists' : 'missing'));
    error_log("Table check - clients: " . ($tables['clients'] ? 'exists' : 'missing'));
    
    if (!$tables['ot_head']) {
        throw new Exception('جدول ot_head غير موجود');
    }
    
    // First, check if there are any orders at all
    $countQuery = "SELECT COUNT(*) as order_count FROM ot_head WHERE isdeleted = 0";
    $countResult = $conn->query($countQuery);
    $orderCount = $countResult ? $countResult->fetch_assoc()['order_count'] : 0;
    error_log("Total non-deleted orders: " . $orderCount);

    // First, let's check if there are any records in ot_head at all
    $countQuery = "SELECT COUNT(*) as total FROM ot_head";
    $countResult = $conn->query($countQuery);
    $totalRecords = $countResult ? $countResult->fetch_assoc()['total'] : 0;
    error_log("Total records in ot_head: " . $totalRecords);
    
    // Get a list of all columns in ot_head
    $columnsQuery = "SHOW COLUMNS FROM ot_head";
    $columnsResult = $conn->query($columnsQuery);
    $columns = [];
    if ($columnsResult) {
        while ($row = $columnsResult->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
        error_log("Columns in ot_head: " . implode(', ', $columns));
    } else {
        error_log("Could not get column information: " . $conn->error);
    }
    
    // Query to get the last 10 POS orders only (pro_tybe = 9)
    $query = "SELECT 
                o.id,
                COALESCE(o.pro_num, CONCAT('ORD-', o.id)) as invoice_number,
                o.pro_date as date,
                o.fat_total as total,
                CASE 
                    WHEN o.closed = 1 THEN 'مكتمل'
                    ELSE 'قيد التنفيذ'
                END as status,
                o.acc2 as client_id,
                COALESCE((SELECT aname FROM acc_head WHERE id = o.acc1 LIMIT 1), 'عميل نقدي') as customer_name,
                o.isdeleted,
                o.pro_tybe as order_type,
                o.info as order_info,
                o.age as order_age
              FROM ot_head o
              WHERE o.isdeleted = 0 AND o.pro_tybe = 9
              ORDER BY o.id DESC
              LIMIT 10";
              
    error_log("Executing query: " . $query);
              
    error_log("Executing query: " . $query);
              
    // Log the query for debugging
    error_log("Executing query: " . $query);
    $result = $conn->query($query);
    
    if ($result === false) {
        $error = 'فشل في استرجاع البيانات: ' . $conn->error . ' - Query: ' . $query;
        error_log($error);
        throw new Exception($error);
    }
    
    $orders = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Format date if needed
            if (!empty($row['date'])) {
                $row['date'] = date('Y-m-d H:i', strtotime($row['date']));
            }
            // Add debug info
            $row['_debug'] = [
                'client_id' => $row['client_id'] ?? null,
                'has_client' => !empty($row['client_id']),
                'isdeleted' => $row['isdeleted'] ?? null
            ];
            $orders[] = $row;
        }
        error_log("Found " . count($orders) . " orders");
    } else {
        error_log("No orders found. Query returned " . ($result ? $result->num_rows : 'no') . " rows");
        
        // Try a simpler query to see if any orders exist at all
        $simpleQuery = "SELECT id, invoice_number, date, total, isdeleted FROM ot_head ORDER BY id DESC LIMIT 5";
        $simpleResult = $conn->query($simpleQuery);
        
        if ($simpleResult && $simpleResult->num_rows > 0) {
            error_log("But found orders with simple query. Sample:");
            while ($row = $simpleResult->fetch_assoc()) {
                error_log("Order ID: " . $row['id'] . ", Invoice: " . $row['invoice_number'] . ", Date: " . $row['date'] . ", isdeleted: " . $row['isdeleted']);
            }
        } else {
            error_log("No orders found in ot_head table at all");
        }
    }
    
    // Log the final orders data
    error_log("Found " . count($orders) . " orders");
    
    // If no orders found, try a more basic query
    if (empty($orders)) {
        error_log("No orders found with first query, trying a more basic query...");
        $basicQuery = "SELECT * FROM ot_head WHERE isdeleted = 0 LIMIT 10";
        $basicResult = $conn->query($basicQuery);
        
        if ($basicResult && $basicResult->num_rows > 0) {
            $orders = [];
            while ($row = $basicResult->fetch_assoc()) {
                $orders[] = [
                    'id' => $row['id'],
                    'invoice_number' => $row['pro_num'],
                    'date' => $row['pro_date'],
                    'total' => $row['fat_total'],
                    'status' => $row['closed'] == 1 ? 'مكتمل' : 'قيد التنفيذ',
                    'client_id' => $row['acc2'],
                    'customer_name' => 'عميل نقدي',
                    'isdeleted' => $row['isdeleted']
                ];
            }
            error_log("Found " . count($orders) . " orders with basic query");
        } else {
            error_log("No orders found with basic query either");
        }
    }
    
    // Clear any previous output
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    // Format the orders data for display
    $formattedOrders = [];
    
    // Check if we have any orders
    if (empty($orders)) {
        // Try one more time with the most basic query possible
        $basicQuery = "SELECT id, pro_num, pro_date, fat_total, closed, acc2, isdeleted 
                      FROM ot_head 
                      ORDER BY id DESC 
                      LIMIT 10";
        $basicResult = $conn->query($basicQuery);
        
        if ($basicResult && $basicResult->num_rows > 0) {
            while ($row = $basicResult->fetch_assoc()) {
                $orders[] = [
                    'id' => $row['id'],
                    'invoice_number' => $row['pro_num'] ?: 'ORD-' . $row['id'],
                    'date' => $row['pro_date'],
                    'total' => $row['fat_total'],
                    'status' => ($row['closed'] == 1) ? 'مكتمل' : 'قيد التنفيذ',
                    'client_id' => $row['acc2'],
                    'customer_name' => 'عميل نقدي',
                    'isdeleted' => $row['isdeleted']
                ];
            }
            error_log("Found " . count($orders) . " orders with basic query");
        }
    }
    foreach ($orders as $order) {
        // Use order info if available, otherwise use default
        $orderTitle = !empty($order['order_info']) ? $order['order_info'] : 'طلب #' . $order['id'];
        
        // Determine order age type
        $orderAgeType = '';
        switch($order['order_age'] ?? 1) {
            case 1: $orderAgeType = 'تيك أواي'; break;
            case 2: $orderAgeType = 'طاولة'; break;
            case 3: $orderAgeType = 'دليفري'; break;
            default: $orderAgeType = 'تيك أواي';
        }
        
        $formattedOrders[] = [
            'id' => $order['id'],
            'invoice_number' => $order['invoice_number'],
            'date' => $order['date'],
            'total' => number_format($order['total'], 2),
            'status' => $order['status'],
            'client_id' => $order['client_id'],
            'customer_name' => $order['customer_name'],
            'title' => $orderTitle,
            'type' => $orderAgeType
        ];
    }
    
    $response = [
        'success' => true,
        'orders' => $formattedOrders,
        'debug' => [
            'query' => $query,
            'num_orders' => count($formattedOrders),
            'pro_types' => $proTypes ?? null
        ]
    ];
    
    // Log the response for debugging
    error_log("Sending response: " . json_encode($response, JSON_UNESCAPED_UNICODE));
    
    error_log("Sending response: " . print_r($response, true));
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Log the error
    $errorMsg = 'Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
    error_log($errorMsg);
    
    // Clear any previous output
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    // Send error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ: ' . $e->getMessage(),
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

// Close the database connection
if (isset($conn) && $conn) {
    $conn->close();
}

// Ensure no extra output
if (ob_get_level() > 0) {
    ob_end_flush();
}

exit;?>
