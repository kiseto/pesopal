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
    // First, check if user exists
    $user_check = "SELECT COUNT(*) as count FROM users WHERE email = 'john.doe@example.com'";
    $stmt = $db->prepare($user_check);
    $stmt->execute();
    $user_exists = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    
    if (!$user_exists) {
        // Insert the user first
        $insert_user = "INSERT INTO users (first_name, last_name, email, phone, birthday, password_hash) VALUES 
                       ('Earl', 'Cappili', 'earl@example.com', '+639171234567', '1990-01-15', ?)";
        $stmt = $db->prepare($insert_user);
        $password_hash = password_hash('password123', PASSWORD_DEFAULT);
        $stmt->execute([$password_hash]);
        $user_id = $db->lastInsertId();
        echo "Created user with ID: " . $user_id . "\n";
    } else {
        // Get existing user
        $get_user = "SELECT id FROM users WHERE email = 'john.doe@example.com' OR email = 'earl@example.com' LIMIT 1";
        $stmt = $db->prepare($get_user);
        $stmt->execute();
        $user_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
        echo "Using existing user ID: " . $user_id . "\n";
    }
    
    // Clear existing transactions for this user
    $clear_trans = "DELETE FROM transactions WHERE user_id = ?";
    $stmt = $db->prepare($clear_trans);
    $stmt->execute([$user_id]);
    
    // Insert fresh sample transactions
    $insert_transactions = "INSERT INTO transactions (user_id, type, description, category, amount, transaction_date) VALUES 
                           (?, 'income', 'Salary', 'Salary', 50000.00, '2025-09-15'),
                           (?, 'income', 'Freelance Work', 'Freelance', 15000.00, '2025-09-20'),
                           (?, 'expense', 'Groceries', 'Food', 2200.00, '2025-09-20'),
                           (?, 'expense', 'Electricity Bill', 'Bills', 1500.00, '2025-09-21'),
                           (?, 'expense', 'Grab Ride', 'Transport', 250.00, '2025-09-22'),
                           (?, 'expense', 'Netflix Subscription', 'Entertainment', 370.00, '2025-09-23'),
                           (?, 'expense', 'Pharmacy', 'Health', 890.00, '2025-09-25')";
    
    $stmt = $db->prepare($insert_transactions);
    $stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id]);
    
    // Clear and insert savings goals
    $clear_goals = "DELETE FROM savings_goals WHERE user_id = ?";
    $stmt = $db->prepare($clear_goals);
    $stmt->execute([$user_id]);
    
    $insert_goals = "INSERT INTO savings_goals (user_id, title, target_amount, saved_amount, target_date, color, icon_class) VALUES 
                    (?, 'Emergency Fund', 120000.00, 78500.00, '2024-12-31', '#2563eb', 'bg-blue-100 text-blue-600 rounded-full p-1'),
                    (?, 'Vacation Fund', 50000.00, 32000.00, '2024-06-30', '#22c55e', 'bg-green-100 text-green-600 rounded-full p-1'),
                    (?, 'New Laptop', 80000.00, 45000.00, '2024-03-31', '#a855f7', 'bg-purple-100 text-purple-600 rounded-full p-1')";
    
    $stmt = $db->prepare($insert_goals);
    $stmt->execute([$user_id, $user_id, $user_id]);
    
    // Now calculate the totals
    $totals_query = "SELECT 
                        (SELECT COALESCE(SUM(amount), 0) FROM transactions WHERE user_id = ? AND type = 'income') as total_income,
                        (SELECT COALESCE(SUM(amount), 0) FROM transactions WHERE user_id = ? AND type = 'expense') as total_expenses,
                        (SELECT COALESCE(SUM(saved_amount), 0) FROM savings_goals WHERE user_id = ?) as total_savings";
    
    $stmt = $db->prepare($totals_query);
    $stmt->execute([$user_id, $user_id, $user_id]);
    $totals = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $balance = $totals['total_income'] - $totals['total_expenses'];
    
    echo json_encode([
        'success' => true,
        'message' => 'Sample data inserted successfully',
        'user_id' => $user_id,
        'totals' => [
            'income' => floatval($totals['total_income']),
            'expenses' => floatval($totals['total_expenses']),
            'balance' => $balance,
            'savings' => floatval($totals['total_savings'])
        ]
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>