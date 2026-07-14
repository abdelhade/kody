<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = $_POST['error'] ?? 'Unknown error';
    $url = $_POST['url'] ?? '';
    $line = $_POST['line'] ?? '';
    $col = $_POST['col'] ?? '';
    $stack = $_POST['stack'] ?? '';
    
    $log = "[" . date('Y-m-d H:i:s') . "] Error: $error in $url on line $line:$col\nStack: $stack\n\n";
    file_put_contents(__DIR__ . '/../js_error.log', $log, FILE_APPEND);
    echo json_encode(['success' => true]);
}
?>
