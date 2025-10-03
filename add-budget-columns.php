<?php
// Add missing columns to transactions table
try {
    // Direct PDO connection (using mysqli as backup)
    $host = 'localhost';
    $dbname = 'pesopal';
    $username = 'root';
    $password = '';
    
    // Try mysqli connection since PDO might not work
    $conn = new mysqli($host, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "Connected successfully to database: $dbname\n\n";
    
    // Check current table structure
    echo "=== CURRENT TRANSACTIONS TABLE STRUCTURE ===\n";
    $result = $conn->query("DESCRIBE transactions");
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
        echo "- {$row['Field']}: {$row['Type']} (Null: {$row['Null']}, Default: {$row['Default']})\n";
    }
    
    // Check if columns exist
    $hasBudgetCategoryId = in_array('budget_category_id', $columns);
    $hasSavingsGoalId = in_array('savings_goal_id', $columns);
    
    echo "\n=== COLUMN CHECK ===\n";
    echo "budget_category_id exists: " . ($hasBudgetCategoryId ? "YES" : "NO") . "\n";
    echo "savings_goal_id exists: " . ($hasSavingsGoalId ? "YES" : "NO") . "\n";
    
    // Add missing columns
    if (!$hasBudgetCategoryId) {
        echo "\n=== ADDING budget_category_id COLUMN ===\n";
        $sql = "ALTER TABLE transactions ADD COLUMN budget_category_id INT NULL, ADD FOREIGN KEY (budget_category_id) REFERENCES budget_categories(id) ON DELETE SET NULL";
        if ($conn->query($sql) === TRUE) {
            echo "✅ budget_category_id column added successfully\n";
        } else {
            echo "❌ Error adding budget_category_id column: " . $conn->error . "\n";
        }
    }
    
    if (!$hasSavingsGoalId) {
        echo "\n=== ADDING savings_goal_id COLUMN ===\n";
        $sql = "ALTER TABLE transactions ADD COLUMN savings_goal_id INT NULL, ADD FOREIGN KEY (savings_goal_id) REFERENCES savings_goals(id) ON DELETE SET NULL";
        if ($conn->query($sql) === TRUE) {
            echo "✅ savings_goal_id column added successfully\n";
        } else {
            echo "❌ Error adding savings_goal_id column: " . $conn->error . "\n";
        }
    }
    
    if ($hasBudgetCategoryId && $hasSavingsGoalId) {
        echo "\n✅ All required columns already exist!\n";
    }
    
    // Show final table structure
    echo "\n=== FINAL TRANSACTIONS TABLE STRUCTURE ===\n";
    $result = $conn->query("DESCRIBE transactions");
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['Field']}: {$row['Type']} (Null: {$row['Null']}, Default: {$row['Default']})\n";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
