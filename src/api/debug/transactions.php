<?php
session_start();

// Set JSON headers
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

// Turn off error reporting
error_reporting(0);
ini_set('display_errors', 0);

try {
    require_once '../config/database.php';
    
    $db = Database::getConnection();
    
    // Check total transactions in database
    $total_query = "SELECT COUNT(*) as total FROM transactions";
    $total_stmt = $db->prepare($total_query);
    $total_stmt->execute();
    $total_result = $total_stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check users
    $user_query = "SELECT id, email, first_name FROM users";
    $user_stmt = $db->prepare($user_query);
    $user_stmt->execute();
    $users = $user_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Check current session
    $session_user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'No session';
    
    // Get all transactions with user info
    $trans_query = "SELECT t.*, u.email FROM transactions t JOIN users u ON t.user_id = u.id LIMIT 10";
    $trans_stmt = $db->prepare($trans_query);
    $trans_stmt->execute();
    $transactions = $trans_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Debug info retrieved',
        'data' => [
            'total_transactions' => $total_result['total'],
            'current_session_user_id' => $session_user,
            'users' => $users,
            'sample_transactions' => $transactions
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Debug error: ' . $e->getMessage()]);
    http_response_code(500);
}
?>