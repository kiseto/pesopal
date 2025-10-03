<?php
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5174');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'User not authenticated']);
        http_response_code(401);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    $host = 'localhost';
    $dbname = 'pesopal';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get total income and expenses
    $incomeStmt = $pdo->prepare("SELECT SUM(amount) as total_income FROM transactions WHERE user_id = :user_id AND type = 'income'");
    $incomeStmt->bindParam(':user_id', $user_id);
    $incomeStmt->execute();
    $income = $incomeStmt->fetch(PDO::FETCH_ASSOC);
    $totalIncome = (float)($income['total_income'] ?? 0);
    
    $expenseStmt = $pdo->prepare("SELECT SUM(amount) as total_expenses FROM transactions WHERE user_id = :user_id AND type = 'expense'");
    $expenseStmt->bindParam(':user_id', $user_id);
    $expenseStmt->execute();
    $expense = $expenseStmt->fetch(PDO::FETCH_ASSOC);
    $totalExpenses = (float)($expense['total_expenses'] ?? 0);
    
    // Calculate balance
    $totalBalance = $totalIncome - $totalExpenses;
    
    // Get savings (for now, use a portion of balance as savings)
    $totalSavings = max(0, $totalBalance * 0.3); // 30% of positive balance as savings
    
    // Get recent transactions
    $recentStmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 5");
    $recentStmt->bindParam(':user_id', $user_id);
    $recentStmt->execute();
    $recentTransactions = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format recent transactions
    $formattedTransactions = [];
    foreach ($recentTransactions as $transaction) {
        $formattedTransactions[] = [
            'description' => $transaction['description'],
            'amount' => $transaction['amount'],
            'type' => $transaction['type'],
            'date' => date('M d', strtotime($transaction['date'])),
            'formatted_amount' => ($transaction['type'] === 'income' ? '+' : '-') . '₱' . number_format($transaction['amount'], 2)
        ];
    }
    
    // Sample chart data (you can enhance this with real data later)
    $chartData = [
        'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        'expenses' => [1200, 1900, 3000, 500, 2000, 3000, 1500]
    ];
    
    echo json_encode([
        'success' => true,
        'data' => [
            'summary' => [
                'total_balance' => $totalBalance,
                'total_income' => $totalIncome,
                'total_expenses' => $totalExpenses,
                'total_savings' => $totalSavings
            ],
            'recent_transactions' => $formattedTransactions,
            'savings_goals' => [
                [
                    'title' => 'Emergency Fund',
                    'saved_amount' => 78500,
                    'target_amount' => 120000,
                    'progress_text' => '₱78,500 / ₱120,000'
                ],
                [
                    'title' => 'Vacation Fund',
                    'saved_amount' => 32000,
                    'target_amount' => 50000,
                    'progress_text' => '₱32,000 / ₱50,000'
                ]
            ],
            'chart_data' => $chartData
        ]
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    http_response_code(500);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
}
?>