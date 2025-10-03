<?php
session_start();

// Set JSON headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
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
    
    if (!$input) {
        echo json_encode(['success' => false, 'message' => 'No data provided']);
        http_response_code(400);
        exit;
    }
    
    // Validate required fields
    if (!isset($input['id']) || !isset($input['title']) || !isset($input['category']) || 
        !isset($input['amount']) || !isset($input['receipt_date'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        http_response_code(400);
        exit;
    }
    
    // Check if file_path should be updated
    $updateFilePathQuery = "";
    $updateFilePathParam = "";
    if (isset($input['file_path']) && !empty($input['file_path'])) {
        $updateFilePathQuery = ", file_path = :file_path";
        $updateFilePathParam = $input['file_path'];
    }
    
    // Direct PDO connection
    $host = 'localhost';
    $dbname = 'pesopal';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Update the receipt
    $query = "UPDATE receipts SET 
              title = :title, 
              category = :category, 
              amount = :amount, 
              receipt_date = :receipt_date, 
              notes = :notes" . $updateFilePathQuery . "
              WHERE id = :id AND user_id = :user_id";
    
    // Debug: Log the query and parameters
    error_log("UPDATE RECEIPT DEBUG: User ID = " . $user_id);
    error_log("UPDATE RECEIPT DEBUG: Receipt ID = " . $input['id']);
    error_log("UPDATE RECEIPT DEBUG: Input = " . json_encode($input));
              
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $input['id'], PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':title', $input['title']);
    $stmt->bindParam(':category', $input['category']);
    $stmt->bindParam(':amount', $input['amount']);
    $stmt->bindParam(':receipt_date', $input['receipt_date']);
    $notes = isset($input['notes']) ? $input['notes'] : '';
    $stmt->bindParam(':notes', $notes);
    
    // Bind file_path parameter if needed
    if (!empty($updateFilePathParam)) {
        $stmt->bindParam(':file_path', $updateFilePathParam);
    }
    
    $result = $stmt->execute();
    
    if ($result && $stmt->rowCount() > 0) {
        // Return the updated receipt data
        $selectQuery = "SELECT * FROM receipts WHERE id = :id AND user_id = :user_id";
        $selectStmt = $pdo->prepare($selectQuery);
        $selectStmt->bindParam(':id', $input['id']);
        $selectStmt->bindParam(':user_id', $user_id);
        $selectStmt->execute();
        
        $updatedReceipt = $selectStmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Receipt updated successfully',
            'data' => [
                'id' => (int)$updatedReceipt['id'],
                'title' => $updatedReceipt['title'],
                'category' => $updatedReceipt['category'],
                'amount' => (float)$updatedReceipt['amount'],
                'date' => $updatedReceipt['receipt_date'],
                'receipt' => 'No file',
                'notes' => $updatedReceipt['notes'],
                'actions' => ''
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Receipt not found or no changes made']);
        http_response_code(404);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
}
?>