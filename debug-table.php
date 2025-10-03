<?php
// Simple debug script to check table structure
require_once 'src/api/config/database.php';

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Database connection failed\n");
}

try {
    echo "=== Checking budget_categories table structure ===\n";
    
    // Check table structure
    $result = $conn->query("DESCRIBE budget_categories");
    echo "Table structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['Field']}: {$row['Type']} (Null: {$row['Null']}, Default: {$row['Default']})\n";
    }
    
    echo "\n=== Checking current data ===\n";
    
    // Check current data
    $result = $conn->query("SELECT id, user_id, category_name, budgeted_amount, spent_amount FROM budget_categories LIMIT 5");
    echo "Current budget categories:\n";
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']}, User: {$row['user_id']}, Category: {$row['category_name']}, Budgeted: {$row['budgeted_amount']}, Spent: {$row['spent_amount']}\n";
    }
    
    echo "\n=== Checking transactions table ===\n";
    
    // Check transactions table
    $result = $conn->query("DESCRIBE transactions");
    echo "Transactions table structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['Field']}: {$row['Type']} (Null: {$row['Null']}, Default: {$row['Default']})\n";
    }
    
    // Check recent transactions
    $result = $conn->query("SELECT id, user_id, amount, budget_category_id, savings_goal_id, created_at FROM transactions ORDER BY created_at DESC LIMIT 5");
    echo "\nRecent transactions:\n";
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']}, User: {$row['user_id']}, Amount: {$row['amount']}, Budget Cat: {$row['budget_category_id']}, Savings Goal: {$row['savings_goal_id']}, Date: {$row['created_at']}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
