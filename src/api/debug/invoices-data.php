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

try {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'User not authenticated']);
        http_response_code(401);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Direct PDO connection
    $host = 'localhost';
    $dbname = 'pesopal';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check what's in the database for this user
    $debug_data = [];
    
    // 1. Check current user
    $debug_data['current_user_id'] = $user_id;
    
    // 2. Check all invoices in database
    $all_invoices_query = "SELECT id, user_id, title, category, amount, due_date, status FROM invoices ORDER BY id";
    $all_stmt = $pdo->prepare($all_invoices_query);
    $all_stmt->execute();
    $debug_data['all_invoices_in_db'] = $all_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 3. Check invoices for current user
    $user_invoices_query = "SELECT id, title, category, amount, due_date, status, notes FROM invoices WHERE user_id = :user_id ORDER BY id";
    $user_stmt = $pdo->prepare($user_invoices_query);
    $user_stmt->bindParam(':user_id', $user_id);
    $user_stmt->execute();
    $debug_data['user_invoices'] = $user_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 4. Check all receipts in database
    $all_receipts_query = "SELECT id, user_id, title, category, amount, receipt_date FROM receipts ORDER BY id";
    $receipts_stmt = $pdo->prepare($all_receipts_query);
    $receipts_stmt->execute();
    $debug_data['all_receipts_in_db'] = $receipts_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 5. Check receipts for current user
    $user_receipts_query = "SELECT id, title, category, amount, receipt_date, notes FROM receipts WHERE user_id = :user_id ORDER BY id";
    $user_receipts_stmt = $pdo->prepare($user_receipts_query);
    $user_receipts_stmt->bindParam(':user_id', $user_id);
    $user_receipts_stmt->execute();
    $debug_data['user_receipts'] = $user_receipts_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 6. Check users table
    $users_query = "SELECT id, email, first_name FROM users ORDER BY id";
    $users_stmt = $pdo->prepare($users_query);
    $users_stmt->execute();
    $debug_data['all_users'] = $users_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'message' => 'Debug info retrieved', 'data' => $debug_data]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    http_response_code(500);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
}
?>