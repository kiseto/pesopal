<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pesopal;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== CHECKING BUDGET CATEGORIES TABLE ===\n";
    
    // Check all data in table
    $stmt = $pdo->query("SELECT * FROM budget_categories");
    $allCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total categories in database: " . count($allCategories) . "\n\n";
    
    foreach ($allCategories as $cat) {
        echo "ID: {$cat['id']}, User: {$cat['user_id']}, Title: {$cat['title']}\n";
        echo "  Allocated: {$cat['allocated_amount']}, Spent: {$cat['spent_amount']}\n";
        echo "  Created: {$cat['created_at']}\n\n";
    }
    
    // Check specifically for user 2
    echo "=== USER 2 CATEGORIES ===\n";
    $stmt = $pdo->prepare("SELECT * FROM budget_categories WHERE user_id = 2");
    $stmt->execute();
    $user2Categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "User 2 categories: " . count($user2Categories) . "\n";
    foreach ($user2Categories as $cat) {
        echo "- {$cat['title']}: ₱{$cat['spent_amount']} / ₱{$cat['allocated_amount']}\n";
    }
    
    if (count($user2Categories) == 0) {
        echo "\n🔴 NO DATA FOUND FOR USER 2!\n";
        echo "Creating data now...\n";
        
        // Create simple data
        $stmt = $pdo->prepare("INSERT INTO budget_categories (user_id, title, allocated_amount, spent_amount) VALUES (2, 'Food & Dining', 15000, 11883)");
        if ($stmt->execute()) {
            echo "✅ Created test category!\n";
        } else {
            echo "❌ Failed to create test category\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>