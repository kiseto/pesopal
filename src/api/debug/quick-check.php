<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');  
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    // Simple query to check if sample data exists
    $query = "SELECT 
                (SELECT COUNT(*) FROM users) as users_count,
                (SELECT COUNT(*) FROM transactions) as transactions_count,
                (SELECT COUNT(*) FROM savings_goals) as goals_count,
                (SELECT SUM(amount) FROM transactions WHERE type = 'income') as total_income,
                (SELECT SUM(amount) FROM transactions WHERE type = 'expense') as total_expenses";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get some sample transactions
    $trans_query = "SELECT description, amount, type, transaction_date FROM transactions LIMIT 5";
    $trans_stmt = $db->prepare($trans_query);
    $trans_stmt->execute();
    $transactions = $trans_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'counts' => $result,
        'sample_transactions' => $transactions,
        'calculated_balance' => $result['total_income'] - $result['total_expenses']
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>