<?php
session_start();

// Set JSON headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

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
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['id'])) {
        echo json_encode(['success' => false, 'message' => 'Invoice ID not provided']);
        http_response_code(400);
        exit;
    }
    
    $invoice_id = $input['id'];
    
    // Direct PDO connection
    $host = 'localhost';
    $dbname = 'pesopal';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // First check if the invoice exists and belongs to the user
    $checkQuery = "SELECT id FROM invoices WHERE id = :id AND user_id = :user_id";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->bindParam(':id', $invoice_id);
    $checkStmt->bindParam(':user_id', $user_id);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Invoice not found or access denied']);
        http_response_code(404);
        exit;
    }
    
    // Delete the invoice
    $deleteQuery = "DELETE FROM invoices WHERE id = :id AND user_id = :user_id";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->bindParam(':id', $invoice_id);
    $deleteStmt->bindParam(':user_id', $user_id);
    
    $result = $deleteStmt->execute();
    
    if ($result && $deleteStmt->rowCount() > 0) {
        echo json_encode([
            'success' => true, 
            'message' => 'Invoice deleted successfully',
            'deleted_id' => (int)$invoice_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete invoice']);
        http_response_code(500);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
}
?>