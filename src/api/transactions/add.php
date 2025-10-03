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
    if (!isset($input['description']) || !isset($input['category']) || !isset($input['amount']) || !isset($input['type']) || !isset($input['date'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        http_response_code(400);
        exit;
    }
    
    $description = trim($input['description']);
    $category = trim($input['category']);
    $amount = abs(floatval($input['amount'])); // Always store as positive
    $type = strtolower(trim($input['type'])); // 'income' or 'expense'
    $date = $input['date'];
    
    // Handle budget/savings allocation data
    $budget_category_id = isset($input['budget_category_id']) ? intval($input['budget_category_id']) : null;
    $savings_goal_id = isset($input['savings_goal_id']) ? intval($input['savings_goal_id']) : null;
    
    // Validate type
    if (!in_array($type, ['income', 'expense'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid transaction type']);
        http_response_code(400);
        exit;
    }
    
    // Validate amount
    if ($amount <= 0) {
        echo json_encode(['success' => false, 'message' => 'Amount must be greater than 0']);
        http_response_code(400);
        exit;
    }
    
    // Insert transaction
    $query = "INSERT INTO transactions (user_id, description, category, amount, type, transaction_date, budget_category_id, savings_goal_id) 
              VALUES (:user_id, :description, :category, :amount, :type, :transaction_date, :budget_category_id, :savings_goal_id)";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':transaction_date', $date);
    $stmt->bindParam(':budget_category_id', $budget_category_id);
    $stmt->bindParam(':savings_goal_id', $savings_goal_id);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':transaction_date', $date);
    
    if ($stmt->execute()) {
        $transaction_id = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Transaction added successfully',
            'data' => [
                'id' => $transaction_id,
                'description' => $description,
                'category' => $category,
                'amount' => $type === 'expense' ? -$amount : $amount,
                'type' => $type,
                'date' => $date
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add transaction']);
        http_response_code(500);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    http_response_code(500);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error adding transaction: ' . $e->getMessage()]);
    http_response_code(500);
}
?>