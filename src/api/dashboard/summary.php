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
    $stmt = $pdo->prepare("SELECT description, amount, type, created_at FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$user_id]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $formattedTransactions = [];
    foreach ($transactions as $t) {
        $formattedTransactions[] = [
            'description' => $t['description'],
            'amount' => (float)$t['amount'],
            'type' => $t['type'],
            'date' => date('M d', strtotime($t['created_at'])),
            'formatted_amount' => ($t['type'] === 'income' ? '+' : '-') . '₱' . number_format($t['amount'], 2)
        ];
    }
    
    // Get savings goals from database
    $stmt = $pdo->prepare("SELECT title, target_amount, saved_amount FROM savings_goals WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $savingsGoals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $formattedSavingsGoals = [];
    foreach ($savingsGoals as $goal) {
        $formattedSavingsGoals[] = [
            'title' => $goal['title'],
            'saved_amount' => (float)$goal['saved_amount'],
            'target_amount' => (float)$goal['target_amount'],
            'progress_text' => '₱' . number_format($goal['saved_amount'], 0) . ' / ₱' . number_format($goal['target_amount'], 0)
        ];
    }
    
    // Get weekly expense data (last 7 days)
    $weeklyExpenses = [];
    $weeklyLabels = [];
    
    // Get expenses for each day of the last 7 days
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $dayName = date('D', strtotime("-$i days")); // Mon, Tue, etc.
        
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as daily_expense 
            FROM transactions 
            WHERE user_id = ? 
            AND type = 'expense' 
            AND DATE(created_at) = ?
        ");
        $stmt->execute([$user_id, $date]);
        $dailyExpense = (float)$stmt->fetchColumn();
        
        $weeklyLabels[] = $dayName;
        $weeklyExpenses[] = $dailyExpense;
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
            'savings_goals' => $formattedSavingsGoals,
            'chart_data' => [
                'labels' => $weeklyLabels,
                'expenses' => $weeklyExpenses
            ],
            'debug' => [
                'user_id' => $user_id,
                'session_exists' => isset($_SESSION['user_id']),
                'transactions_count' => count($transactions),
                'weekly_data' => array_combine($weeklyLabels, $weeklyExpenses)
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