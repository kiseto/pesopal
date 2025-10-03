<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pesopal;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== SAVINGS_GOALS TABLE STRUCTURE ===\n";
    $stmt = $pdo->query("DESCRIBE savings_goals");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-20s %-15s %-10s %-10s %-15s %s\n", 
            $row['Field'], 
            $row['Type'], 
            $row['Null'], 
            $row['Key'], 
            $row['Default'] ?? 'NULL', 
            $row['Extra']
        );
    }
    
    echo "\n=== SAVINGS GOALS DATA (USER 2) ===\n";
    $stmt = $pdo->prepare("SELECT * FROM savings_goals WHERE user_id = 2");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    
    echo "\n=== ALL SAVINGS GOALS DATA ===\n";
    $stmt = $pdo->query("SELECT * FROM savings_goals");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>