<?php
// Check transactions table structure
$host = 'localhost';
$dbname = 'pesopal';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Transactions Table Structure</h2>";
    $result = $pdo->query("DESCRIBE transactions");
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['Field']}: {$row['Type']}<br>";
    }
    
    echo "<br><h2>Sample Transaction Data</h2>";
    $result = $pdo->query("SELECT * FROM transactions LIMIT 3");
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['id']}, Date: {$row['date']}, Amount: {$row['amount']}, Type: {$row['type']}<br>";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
