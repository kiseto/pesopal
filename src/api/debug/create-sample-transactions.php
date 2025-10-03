<?php
session_start();
require_once '../config/database.php';
require_once '../helpers/ApiHelper.php';

// Set content type to JSON
header('Content-Type: application/json');

try {
    $db = Database::getConnection();
    
    if (!isset($_SESSION['user_id'])) {
        ApiHelper::sendResponse(false, 'User not authenticated', null, 401);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Check if user already has transactions
    $check_query = "SELECT COUNT(*) as count FROM transactions WHERE user_id = :user_id";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':user_id', $user_id);
    $check_stmt->execute();
    $existing_count = $check_stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($existing_count > 0) {
        ApiHelper::sendResponse(true, 'User already has transactions', ['existing_transactions' => $existing_count]);
        exit;
    }
    
    // Insert sample transactions for the current user
    $sample_transactions = [
        ['type' => 'expense', 'description' => 'Groceries', 'category' => 'Food', 'amount' => 2200.00, 'date' => '2025-09-20'],
        ['type' => 'expense', 'description' => 'Electricity', 'category' => 'Bills', 'amount' => 1500.00, 'date' => '2025-09-21'],
        ['type' => 'expense', 'description' => 'Grab ride', 'category' => 'Transport', 'amount' => 250.00, 'date' => '2025-09-22'],
        ['type' => 'expense', 'description' => 'Netflix', 'category' => 'Entertainment', 'amount' => 370.00, 'date' => '2025-09-23'],
        ['type' => 'income', 'description' => 'Salary', 'category' => 'Salary', 'amount' => 50000.00, 'date' => '2025-09-15'],
        ['type' => 'expense', 'description' => 'Pharmacy', 'category' => 'Health', 'amount' => 890.00, 'date' => '2025-09-25'],
        ['type' => 'expense', 'description' => 'Coffee Shop', 'category' => 'Food', 'amount' => 180.00, 'date' => '2025-09-26'],
        ['type' => 'income', 'description' => 'Freelance Work', 'category' => 'Freelance', 'amount' => 15000.00, 'date' => '2025-09-28']
    ];
    
    $insert_query = "INSERT INTO transactions (user_id, type, description, category, amount, transaction_date) 
                     VALUES (:user_id, :type, :description, :category, :amount, :transaction_date)";
    $insert_stmt = $db->prepare($insert_query);
    
    $inserted_count = 0;
    foreach ($sample_transactions as $transaction) {
        $insert_stmt->bindParam(':user_id', $user_id);
        $insert_stmt->bindParam(':type', $transaction['type']);
        $insert_stmt->bindParam(':description', $transaction['description']);
        $insert_stmt->bindParam(':category', $transaction['category']);
        $insert_stmt->bindParam(':amount', $transaction['amount']);
        $insert_stmt->bindParam(':transaction_date', $transaction['date']);
        
        if ($insert_stmt->execute()) {
            $inserted_count++;
        }
    }
    
    ApiHelper::sendResponse(true, 'Sample transactions created successfully', [
        'user_id' => $user_id,
        'transactions_inserted' => $inserted_count
    ]);
    
} catch (Exception $e) {
    ApiHelper::sendResponse(false, 'Error creating sample transactions: ' . $e->getMessage(), null, 500);
}
?>