<?php
session_start();

ob_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Get user_id from session or default to user 2 for testing
    $user_id = $_SESSION['user_id'] ?? 2;
    
    // Database connection
    $pdo = new PDO("mysql:host=localhost;dbname=pesopal;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get income
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'income'");
    $stmt->execute([$user_id]);
    $totalIncome = (float)$stmt->fetchColumn();
    
    // Get expenses  
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'expense'");
    $stmt->execute([$user_id]);
    $totalExpenses = (float)$stmt->fetchColumn();
    
    // Calculate totals
    $totalBalance = $totalIncome - $totalExpenses;
    $totalSavings = max(0, $totalBalance * 0.3);
    
    // Get recent transactions
    $stmt = $pdo->prepare("SELECT description, amount, type, date FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$user_id]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $formattedTransactions = [];
    foreach ($transactions as $t) {
        $formattedTransactions[] = [
            'description' => $t['description'],
            'amount' => (float)$t['amount'],
            'type' => $t['type'],
            'date' => date('M d', strtotime($t['date'])),
            'formatted_amount' => ($t['type'] === 'income' ? '+' : '-') . '₱' . number_format($t['amount'], 2)
        ];
    }
    
    // Clear any potential output and send JSON
    ob_clean();
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
                ]
            ],
            'chart_data' => [
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'expenses' => [1200, 1900, 3000, 500, 2000, 3000, 1500]
            ],
            'debug' => [
                'user_id' => $user_id,
                'session_exists' => isset($_SESSION['user_id']),
                'transactions_count' => count($transactions)
            ]
        ]
    ]);
    
} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'user_id' => $user_id ?? 'unknown'
        ]
    ]);
}
?>