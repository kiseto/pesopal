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
    // Test database connection step by step
    
    // Step 1: Check if session exists
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'No user session found', 'session' => $_SESSION]);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Step 2: Test basic database connection
    $host = 'localhost';
    $dbname = 'pesopal';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Step 3: Check if transactions table exists
    $table_check = $pdo->query("SHOW TABLES LIKE 'transactions'");
    if ($table_check->rowCount() == 0) {
        echo json_encode(['success' => false, 'message' => 'Transactions table does not exist']);
        exit;
    }
    
    // Step 4: Test simple query
    $count_query = "SELECT COUNT(*) as total FROM transactions";
    $count_stmt = $pdo->prepare($count_query);
    $count_stmt->execute();
    $total_transactions = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Step 5: Test user-specific query
    $user_query = "SELECT COUNT(*) as user_total FROM transactions WHERE user_id = :user_id";
    $user_stmt = $pdo->prepare($user_query);
    $user_stmt->bindParam(':user_id', $user_id);
    $user_stmt->execute();
    $user_transactions = $user_stmt->fetch(PDO::FETCH_ASSOC)['user_total'];
    
    // Step 6: If no transactions, get sample data
    if ($user_transactions == 0) {
        echo json_encode([
            'success' => true, 
            'message' => 'No transactions found for user',
            'data' => [],
            'debug' => [
                'user_id' => $user_id,
                'total_transactions_in_db' => $total_transactions,
                'user_transactions' => $user_transactions,
                'session' => $_SESSION
            ]
        ]);
        exit;
    }
    
    // Step 7: Get actual transactions
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
    
    // Format transactions
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
    
    echo json_encode([
        'success' => true,
        'message' => 'Transactions retrieved successfully',
        'data' => $formatted_transactions,
        'debug' => [
            'user_id' => $user_id,
            'transaction_count' => count($formatted_transactions)
        ]
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    http_response_code(500);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
}
?>