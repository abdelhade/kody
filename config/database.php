<?php
// Database configuration
$host = '127.0.0.1';
$username = 'u173148011_focua';
$password = 'AbAbAb@1234';
$database = 'u173148011_focus';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode([
        'status' => 'error',
        'message' => 'Database connection failed'
    ]));
}

// Set charset to utf8
$conn->set_charset('utf8mb4');
