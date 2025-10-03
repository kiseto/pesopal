<?php
session_start();

// Set JSON header first
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Simple test response
    echo json_encode([
        'success' => true,
        'message' => 'Test API working',
        'session_id' => session_id(),
        'session_data' => $_SESSION ?? [],
        'server_info' => [
            'php_version' => PHP_VERSION,
            'time' => date('Y-m-d H:i:s')
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Test API error: ' . $e->getMessage()
    ]);
}
?>