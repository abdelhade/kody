<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$_SESSION['login'] = 'TestUser';
$_SESSION['userid'] = 1;

echo "Current Dir: " . getcwd() . "\n";
echo "Testing include includes/pos_simple_header.php\n";

if (file_exists('includes/pos_simple_header.php')) {
    echo "File exists.\n";
    try {
        include('includes/pos_simple_header.php');
        echo "Header included.\n";
        
        if (isset($conn)) {
            echo "DB Connected.\n";
            echo "DB Host: " . $conn->host_info . "\n";
        } else {
            echo "DB NOT Connected.\n";
        }
        
    } catch (Throwable $e) {
        echo "Error: " . $e->getMessage() . "\n";
        echo "Trace: " . $e->getTraceAsString() . "\n";
    }
} else {
    echo "File NOT found.\n";
}
?>
