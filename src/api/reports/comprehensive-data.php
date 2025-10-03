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
    
    // Get range parameter from GET request (default to 365 days)
    $range_days = isset($_GET['range']) ? intval($_GET['range']) : 365;
    
    // Database connection
    $pdo = new PDO("mysql:host=localhost;dbname=pesopal;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get recent transactions within range
    $stmt = $pdo->prepare("
        SELECT description, amount, type, created_at, 
               CASE 
                   WHEN budget_category_id IS NOT NULL THEN (SELECT title FROM budget_categories WHERE id = budget_category_id)
                   ELSE 'Uncategorized'
               END as category
        FROM transactions 
        WHERE user_id = ? 
        AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $stmt->execute([$user_id, $range_days]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format transactions
    $formattedTransactions = [];
    foreach ($transactions as $t) {
        $formattedTransactions[] = [
            'description' => $t['description'],
            'amount' => (float)$t['amount'],
            'type' => $t['type'],
            'category' => $t['category'],
            'date' => date('M d, Y', strtotime($t['created_at'])),
            'formatted_amount' => ($t['type'] === 'income' ? '+' : '-') . '₱' . number_format($t['amount'], 2)
        ];
    }
    
    // Get invoices within range
    $stmt = $pdo->prepare("
        SELECT title, amount, status, due_date, category
        FROM invoices 
        WHERE user_id = ? 
        AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ORDER BY due_date DESC 
        LIMIT 10
    ");
    $stmt->execute([$user_id, $range_days]);
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format invoices
    $formattedInvoices = [];
    foreach ($invoices as $inv) {
        $formattedInvoices[] = [
            'title' => $inv['title'],
            'amount' => (float)$inv['amount'],
            'status' => $inv['status'],
            'category' => $inv['category'],
            'due_date' => date('M d, Y', strtotime($inv['due_date'])),
            'formatted_amount' => '₱' . number_format($inv['amount'], 2)
        ];
    }
    
    // Get budget categories with spending
    $stmt = $pdo->prepare("
        SELECT 
            bc.title,
            bc.allocated_amount,
            COALESCE(SUM(t.amount), 0) as spent_amount,
            bc.allocated_amount - COALESCE(SUM(t.amount), 0) as remaining
        FROM budget_categories bc
        LEFT JOIN transactions t ON bc.id = t.budget_category_id 
            AND t.user_id = ? 
            AND t.type = 'expense'
            AND t.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        WHERE bc.user_id = ?
        GROUP BY bc.id, bc.title, bc.allocated_amount
        ORDER BY bc.title
    ");
    $stmt->execute([$user_id, $range_days, $user_id]);
    $budgetCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format budget categories
    $formattedBudgets = [];
    foreach ($budgetCategories as $budget) {
        $budgetAmount = (float)$budget['allocated_amount'];
        $spentAmount = (float)$budget['spent_amount'];
        $remaining = $budgetAmount - $spentAmount;
        $percentage = $budgetAmount > 0 ? round(($spentAmount / $budgetAmount) * 100, 1) : 0;
        
        $formattedBudgets[] = [
            'title' => $budget['title'],
            'budget_amount' => $budgetAmount,
            'spent_amount' => $spentAmount,
            'remaining' => $remaining,
            'percentage' => $percentage,
            'status' => $percentage > 100 ? 'Over Budget' : ($percentage > 80 ? 'Near Limit' : 'On Track')
        ];
    }
    
    // Get savings goals
    $stmt = $pdo->prepare("
        SELECT title, target_amount, saved_amount
        FROM savings_goals 
        WHERE user_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$user_id]);
    $savingsGoals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format savings goals
    $formattedSavings = [];
    foreach ($savingsGoals as $goal) {
        $targetAmount = (float)$goal['target_amount'];
        $savedAmount = (float)$goal['saved_amount'];
        $percentage = $targetAmount > 0 ? round(($savedAmount / $targetAmount) * 100, 1) : 0;
        
        $formattedSavings[] = [
            'title' => $goal['title'],
            'target_amount' => $targetAmount,
            'saved_amount' => $savedAmount,
            'remaining' => $targetAmount - $savedAmount,
            'percentage' => $percentage,
            'status' => $percentage >= 100 ? 'Completed' : ($percentage >= 50 ? 'In Progress' : 'Getting Started')
        ];
    }
    
    // Calculate summary statistics
    $totalTransactionIncome = array_sum(array_column(array_filter($formattedTransactions, function($t) { return $t['type'] === 'income'; }), 'amount'));
    $totalTransactionExpenses = array_sum(array_column(array_filter($formattedTransactions, function($t) { return $t['type'] === 'expense'; }), 'amount'));
    $totalInvoiceAmount = array_sum(array_column($formattedInvoices, 'amount'));
    $totalBudgetAmount = array_sum(array_column($formattedBudgets, 'budget_amount'));
    $totalSpentAmount = array_sum(array_column($formattedBudgets, 'spent_amount'));
    $totalSavingsTarget = array_sum(array_column($formattedSavings, 'target_amount'));
    $totalSavingsAchieved = array_sum(array_column($formattedSavings, 'saved_amount'));

    // Clear any potential output and send JSON
    ob_clean();
    echo json_encode([
        'success' => true,
        'data' => [
            'transactions' => $formattedTransactions,
            'invoices' => $formattedInvoices,
            'budget_categories' => $formattedBudgets,
            'savings_goals' => $formattedSavings,
            'summary_stats' => [
                'transaction_income' => $totalTransactionIncome,
                'transaction_expenses' => $totalTransactionExpenses,
                'total_invoice_amount' => $totalInvoiceAmount,
                'total_budget_amount' => $totalBudgetAmount,
                'total_spent_amount' => $totalSpentAmount,
                'total_savings_target' => $totalSavingsTarget,
                'total_savings_achieved' => $totalSavingsAchieved,
                'range_days' => $range_days
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
