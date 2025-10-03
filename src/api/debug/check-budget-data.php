<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pesopal;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== BUDGET CATEGORIES TABLE ===\n";
    $stmt = $pdo->query("DESCRIBE budget_categories");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-20s %-15s\n", $row['Field'], $row['Type']);
    }
    
    echo "\n=== BUDGET CATEGORIES DATA ===\n";
    $stmt = $pdo->prepare("SELECT * FROM budget_categories WHERE user_id = 2");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    
    echo "\n=== SAVINGS GOALS TABLE ===\n";
    $stmt = $pdo->query("DESCRIBE savings_goals");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-20s %-15s\n", $row['Field'], $row['Type']);
    }
    
    echo "\n=== SAVINGS GOALS DATA ===\n";
    $stmt = $pdo->prepare("SELECT * FROM savings_goals WHERE user_id = 2");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>