<?php
include('includes/connect.php');

try {
    $conn->query("ALTER TABLE `settings`
        ADD COLUMN `emp_commission` DOUBLE NOT NULL DEFAULT 0,
        ADD COLUMN `user_commission` DOUBLE NOT NULL DEFAULT 0");
    echo 'Done.';
} catch (mysqli_sql_exception $e) {
    if (stripos($e->getMessage(), 'Duplicate column') !== false) {
        echo 'Already exists.';
    } else {
        echo 'Error: ' . htmlspecialchars($e->getMessage());
    }
}
