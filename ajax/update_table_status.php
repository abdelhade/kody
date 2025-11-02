<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Database connection
include('../includes/connect.php');

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get the POST data
$tableId = isset($_POST['table_id']) ? intval($_POST['table_id']) : 0;
$isOccupied = isset($_POST['is_occupied']) ? intval($_POST['is_occupied']) : 0;

// Validate input
if ($tableId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid table ID']);
    exit;
}

try {
    // Update the table status in the database
    $stmt = $conn->prepare("UPDATE tables SET table_case = ? WHERE id = ?");
    $stmt->bind_param('ii', $isOccupied, $tableId);
    
    if ($stmt->execute()) {
        // Log the status update
        $logMessage = "Table #$tableId status updated to " . ($isOccupied ? 'occupied' : 'available');
        error_log($logMessage);
        
        echo json_encode([
            'success' => true,
            'message' => 'Table status updated successfully',
            'table_id' => $tableId,
            'is_occupied' => $isOccupied
        ]);
    } else {
        throw new Exception('Failed to update table status');
    }
    
    $stmt->close();
} catch (Exception $e) {
    error_log('Error updating table status: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error updating table status: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
