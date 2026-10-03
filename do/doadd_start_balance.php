<?php
include('../includes/connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acc_id']) && isset($_POST['newbalance'])) {
        $acc_ids = $_POST['acc_id'];
        $new_balances = $_POST['newbalance'];

        // Prepare the update statement
        $stmt = $conn->prepare("UPDATE acc_head SET balance = ? WHERE id = ?");

        for ($i = 0; $i < count($acc_ids); $i++) {
            $id = (int)$acc_ids[$i];
            
            // Skip if newbalance wasn't provided for this specific account
            if (isset($new_balances[$i])) {
                $balance = (float)$new_balances[$i];
                $stmt->bind_param("di", $balance, $id);
                $stmt->execute();
            }
        }
        $stmt->close();
        
        // Log the process
        $process_stmt = $conn->prepare("INSERT INTO `process`(`type`) VALUES (?)");
        $process_type = "Update start balances";
        $process_stmt->bind_param("s", $process_type);
        $process_stmt->execute();
        $process_stmt->close();
    }
    
    // Redirect back to the start balance page
    header("Location: ../start_balance.php");
    exit();
}
