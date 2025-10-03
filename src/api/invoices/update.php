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
    
    // Debug: Log the received data
    error_log("UPDATE INVOICE DEBUG: User ID = " . $user_id);
    error_log("UPDATE INVOICE DEBUG: Input = " . json_encode($input));
    
    if (!$input) {
        echo json_encode(['success' => false, 'message' => 'No data provided']);
        http_response_code(400);
        exit;
    }
    
    // Validate required fields
    if (!isset($input['id']) || !isset($input['title']) || !isset($input['category']) || 
        !isset($input['amount']) || !isset($input['due_date']) || !isset($input['status'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        http_response_code(400);
        exit;
    }
    
    // Direct PDO connection
    $host = 'localhost';
    $dbname = 'pesopal';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Update the invoice
    $query = "UPDATE invoices SET 
              title = :title, 
              category = :category, 
              amount = :amount, 
              due_date = :due_date, 
              status = :status, 
              notes = :notes 
              WHERE id = :id AND user_id = :user_id";
    
    // Debug: Log the query and parameters
    error_log("UPDATE INVOICE DEBUG: Query = " . $query);
    error_log("UPDATE INVOICE DEBUG: Parameters - ID=" . $input['id'] . ", User=" . $user_id . ", Status=" . $input['status']);
              
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $input['id'], PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':title', $input['title']);
    $stmt->bindParam(':category', $input['category']);
    $stmt->bindParam(':amount', $input['amount']);
    $stmt->bindParam(':due_date', $input['due_date']);
    $stmt->bindParam(':status', $input['status']);
    $notes = isset($input['notes']) ? $input['notes'] : '';
    $stmt->bindParam(':notes', $notes);
    
    $result = $stmt->execute();
    
    // Debug: Log the result
    error_log("UPDATE INVOICE DEBUG: Query executed, result = " . ($result ? 'true' : 'false'));
    error_log("UPDATE INVOICE DEBUG: Rows affected = " . $stmt->rowCount());
    
    if ($result && $stmt->rowCount() > 0) {
        // Return the updated invoice data
        $selectQuery = "SELECT * FROM invoices WHERE id = :id AND user_id = :user_id";
        $selectStmt = $pdo->prepare($selectQuery);
        $selectStmt->bindParam(':id', $input['id']);
        $selectStmt->bindParam(':user_id', $user_id);
        $selectStmt->execute();
        
        $updatedInvoice = $selectStmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Invoice updated successfully',
            'data' => [
                'id' => (int)$updatedInvoice['id'],
                'title' => $updatedInvoice['title'],
                'category' => $updatedInvoice['category'],
                'amount' => (float)$updatedInvoice['amount'],
                'dueDate' => $updatedInvoice['due_date'],
                'status' => $updatedInvoice['status'],
                'notes' => $updatedInvoice['notes'],
                'actions' => ''
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invoice not found or no changes made']);
        http_response_code(404);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
}
?>