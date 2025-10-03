<?php
$host = 'localhost';
$dbname = 'pesopal';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Users and Transactions</h2>";
    
    // Get all users
    $users = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
    echo "<h3>All Users:</h3>";
    foreach ($users as $user) {
        echo "ID: {$user['id']}, Name: {$user['first_name']} {$user['last_name']}, Email: {$user['email']}<br>";
        
        // Check transactions for this user
        $transactions = $pdo->prepare("SELECT COUNT(*) as count, SUM(CASE WHEN type='income' THEN amount ELSE 0 END) as income, SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) as expense FROM transactions WHERE user_id = ?");
        $transactions->execute([$user['id']]);
        $transData = $transactions->fetch(PDO::FETCH_ASSOC);
        echo "&nbsp;&nbsp;- Transactions: {$transData['count']}, Income: ₱{$transData['income']}, Expenses: ₱{$transData['expense']}<br><br>";
    }
    
    // Create transactions for user ID 2 if they don't exist
    $userCheck = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE user_id = 2");
    $userCheck->execute();
    $hasTransactions = $userCheck->fetchColumn();
    
    if ($hasTransactions == 0) {
        echo "<h3>Creating Sample Transactions for User ID 2:</h3>";
        
        $sampleTransactions = [
            ['Salary Payment', 85000, 'income', '2025-10-01'],
            ['Freelance Work', 25000, 'income', '2025-10-02'],
            ['Grocery Shopping', 3500, 'expense', '2025-10-01'],
            ['Gas Station', 2800, 'expense', '2025-10-01'],
            ['Restaurant Dinner', 1200, 'expense', '2025-10-02'],
            ['Online Shopping', 4500, 'expense', '2025-10-02'],
            ['Coffee Shop', 350, 'expense', '2025-10-03'],
            ['Investment Dividend', 5500, 'income', '2025-09-28'],
        ];
        
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, description, amount, type, date, category, created_at) VALUES (?, ?, ?, ?, ?, 'general', NOW())");
        
        foreach ($sampleTransactions as $trans) {
            $stmt->execute([2, $trans[0], $trans[1], $trans[2], $trans[3]]);
        }
        
        echo "Created " . count($sampleTransactions) . " sample transactions for user ID 2<br>";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>