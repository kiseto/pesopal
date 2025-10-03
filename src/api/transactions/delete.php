<?php
session_start();

// Set JSON headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Direct PDO connection
    $host = 'localhost';
    $dbname = 'pesopal';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'User not authenticated']);
        http_response_code(401);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (!isset($input['id'])) {
        echo json_encode(['success' => false, 'message' => 'Transaction ID is required']);
        http_response_code(400);
        exit;
    }
    
    $transaction_id = intval($input['id']);
    
    // First, check if the transaction belongs to the current user
    $checkStmt = $pdo->prepare("SELECT id FROM transactions WHERE id = ? AND user_id = ?");
    $checkStmt->execute([$transaction_id, $user_id]);
    $existingTransaction = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$existingTransaction) {
        echo json_encode(['success' => false, 'message' => 'Transaction not found or access denied']);
        http_response_code(404);
        exit;
    }
    
    // Delete transaction
    $query = "DELETE FROM transactions WHERE id = ? AND user_id = ?";
    
    $stmt = $pdo->prepare($query);
    $result = $stmt->execute([$transaction_id, $user_id]);
    
    if ($result && $stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Transaction deleted successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete transaction']);
        http_response_code(500);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
}
?>
