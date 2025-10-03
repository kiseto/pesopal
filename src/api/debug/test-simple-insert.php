<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pesopal;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Test simple insertion
    echo "Testing simple budget category insertion...\n";
    
    $stmt = $pdo->prepare("INSERT INTO budget_categories (user_id, title, allocated_amount, spent_amount) VALUES (2, 'Test Category', 5000.00, 2000.00)");
    if ($stmt->execute()) {
        echo "Successfully inserted test category!\n";
        echo "Insert ID: " . $pdo->lastInsertId() . "\n";
    } else {
        echo "Failed to insert test category.\n";
        print_r($stmt->errorInfo());
    }
    
    // Check if it exists
    $stmt = $pdo->query("SELECT * FROM budget_categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\nAll categories in database:\n";
    foreach ($categories as $cat) {
        print_r($cat);
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>