<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pesopal;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Checking savings_goals table columns:\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM savings_goals");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Column: " . $row['Field'] . " (Type: " . $row['Type'] . ")\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>