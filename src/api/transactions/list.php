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
    // Use direct database connection instead of Database class
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'User not authenticated']);
        http_response_code(401);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Direct PDO connection (same as working debug version)
    $host = 'localhost';
    $dbname = 'pesopal';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get all transactions for the user
    $query = "SELECT 
                id,
                description,
                category,
                amount,
                type,
                DATE(transaction_date) as date,
                transaction_date
              FROM transactions 
              WHERE user_id = :user_id 
              ORDER BY transaction_date DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format transactions for the frontend
    $formatted_transactions = [];
    foreach ($transactions as $transaction) {
        $formatted_transactions[] = [
            'id' => $transaction['id'],
            'date' => $transaction['date'],
            'category' => $transaction['category'],
            'desc' => $transaction['description'],
            'amount' => $transaction['type'] === 'expense' ? -abs(floatval($transaction['amount'])) : abs(floatval($transaction['amount'])),
            'type' => $transaction['type']
        ];
    }
    
    echo json_encode(['success' => true, 'message' => 'Transactions retrieved successfully', 'data' => $formatted_transactions]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    http_response_code(500);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error retrieving transactions: ' . $e->getMessage()]);
    http_response_code(500);
}
?>