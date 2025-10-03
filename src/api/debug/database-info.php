<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

require_once '../config/database.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

try {
    // Check all tables and their data
    $result = [];
    
    // Check users table
    $users_query = "SELECT COUNT(*) as count FROM users";
    $users_stmt = $db->prepare($users_query);
    $users_stmt->execute();
    $users_count = $users_stmt->fetch(PDO::FETCH_ASSOC)['count'];
    $result['users_count'] = $users_count;
    
    // Get sample user data
    if ($users_count > 0) {
        $user_data_query = "SELECT id, first_name, last_name, email FROM users LIMIT 1";
        $user_data_stmt = $db->prepare($user_data_query);
        $user_data_stmt->execute();
        $result['sample_user'] = $user_data_stmt->fetch(PDO::FETCH_ASSOC);
        
        $user_id = $result['sample_user']['id'];
        
        // Check transactions for this user
        $transactions_query = "SELECT * FROM transactions WHERE user_id = :user_id";
        $transactions_stmt = $db->prepare($transactions_query);
        $transactions_stmt->bindParam(':user_id', $user_id);
        $transactions_stmt->execute();
        $result['transactions'] = $transactions_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Check savings goals
        $goals_query = "SELECT * FROM savings_goals WHERE user_id = :user_id";
        $goals_stmt = $db->prepare($goals_query);
        $goals_stmt->bindParam(':user_id', $user_id);
        $goals_stmt->execute();
        $result['savings_goals'] = $goals_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate totals
        $income_total = 0;
        $expense_total = 0;
        foreach ($result['transactions'] as $transaction) {
            if ($transaction['type'] === 'income') {
                $income_total += $transaction['amount'];
            } else {
                $expense_total += $transaction['amount'];
            }
        }
        
        $result['calculated_totals'] = [
            'income' => $income_total,
            'expenses' => $expense_total,
            'balance' => $income_total - $expense_total
        ];
    }
    
    echo json_encode([
        'success' => true,
        'database_info' => $result
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>