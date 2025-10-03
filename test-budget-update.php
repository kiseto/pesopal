<?php
session_start();

// Direct PDO connection
$host = 'localhost';
$dbname = 'pesopal';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DATABASE CONNECTION TEST ===\n";
    echo "Connected successfully to database: $dbname\n\n";
    
    // Check table structures
    echo "=== BUDGET_CATEGORIES TABLE STRUCTURE ===\n";
    $stmt = $pdo->query("DESCRIBE budget_categories");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['Field']}: {$row['Type']} (Null: {$row['Null']}, Default: {$row['Default']})\n";
    }
    
    echo "\n=== TRANSACTIONS TABLE STRUCTURE ===\n";
    $stmt = $pdo->query("DESCRIBE transactions");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['Field']}: {$row['Type']} (Null: {$row['Null']}, Default: {$row['Default']})\n";
    }
    
    // Check current budget categories
    echo "\n=== CURRENT BUDGET CATEGORIES ===\n";
    $stmt = $pdo->query("SELECT id, user_id, title, allocated_amount, spent_amount FROM budget_categories ORDER BY user_id, id");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['id']}, User: {$row['user_id']}, Title: {$row['title']}, Allocated: {$row['allocated_amount']}, Spent: {$row['spent_amount']}\n";
    }
    
    // Check recent transactions with budget links
    echo "\n=== RECENT TRANSACTIONS WITH BUDGET LINKS ===\n";
    $stmt = $pdo->query("SELECT id, user_id, description, amount, type, budget_category_id, savings_goal_id, created_at FROM transactions ORDER BY created_at DESC LIMIT 10");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['id']}, User: {$row['user_id']}, Description: {$row['description']}, Amount: {$row['amount']}, Type: {$row['type']}, Budget Cat: {$row['budget_category_id']}, Savings Goal: {$row['savings_goal_id']}, Date: {$row['created_at']}\n";
    }
    
    // Test update query
    echo "\n=== TESTING UPDATE QUERY ===\n";
    
    // Find a budget category to test with
    $stmt = $pdo->query("SELECT id, user_id, title, spent_amount FROM budget_categories LIMIT 1");
    $testCategory = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($testCategory) {
        $categoryId = $testCategory['id'];
        $userId = $testCategory['user_id'];
        $currentSpent = $testCategory['spent_amount'];
        
        echo "Testing with Category ID: $categoryId, User: $userId, Current Spent: $currentSpent\n";
        
        // Test update (add 10.00)
        $testAmount = 10.00;
        $updateStmt = $pdo->prepare("UPDATE budget_categories SET spent_amount = spent_amount + ? WHERE id = ? AND user_id = ?");
        $result = $updateStmt->execute([$testAmount, $categoryId, $userId]);
        
        echo "Update result: " . ($result ? "SUCCESS" : "FAILED") . "\n";
        echo "Affected rows: " . $updateStmt->rowCount() . "\n";
        
        // Check new value
        $stmt = $pdo->prepare("SELECT spent_amount FROM budget_categories WHERE id = ? AND user_id = ?");
        $stmt->execute([$categoryId, $userId]);
        $newSpent = $stmt->fetchColumn();
        echo "New spent amount: $newSpent (should be " . ($currentSpent + $testAmount) . ")\n";
        
        // Reset back to original value
        $resetStmt = $pdo->prepare("UPDATE budget_categories SET spent_amount = ? WHERE id = ? AND user_id = ?");
        $resetStmt->execute([$currentSpent, $categoryId, $userId]);
        echo "Reset to original value: $currentSpent\n";
    } else {
        echo "No budget categories found to test with\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
