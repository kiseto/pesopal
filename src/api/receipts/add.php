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
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (!isset($input['title']) || !isset($input['category']) || !isset($input['amount']) || !isset($input['receipt_date'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        http_response_code(400);
        exit;
    }
    
    $title = trim($input['title']);
    $category = trim($input['category']);
    $amount = floatval($input['amount']);
    $receipt_date = $input['receipt_date'];
    $vendor = isset($input['vendor']) ? trim($input['vendor']) : '';
    $notes = isset($input['notes']) ? trim($input['notes']) : '';
    $file_path = isset($input['receipt']) ? trim($input['receipt']) : null;
    
    // Validate amount
    if ($amount <= 0) {
        echo json_encode(['success' => false, 'message' => 'Amount must be greater than 0']);
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
    
    // Insert receipt
    $query = "INSERT INTO receipts (user_id, title, vendor, category, amount, receipt_date, file_path, notes) 
              VALUES (:user_id, :title, :vendor, :category, :amount, :receipt_date, :file_path, :notes)";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':vendor', $vendor);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':receipt_date', $receipt_date);
    $stmt->bindParam(':file_path', $file_path);
    $stmt->bindParam(':notes', $notes);
    
    if ($stmt->execute()) {
        $receipt_id = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Receipt added successfully',
            'data' => [
                'id' => $receipt_id,
                'title' => $title,
                'category' => $category,
                'amount' => $amount,
                'dueDate' => $receipt_date,
                'status' => 'Paid',
                'notes' => $notes,
                'receipt' => $file_path,
                'actions' => ''
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add receipt']);
        http_response_code(500);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    http_response_code(500);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error adding receipt: ' . $e->getMessage()]);
    http_response_code(500);
}
?>