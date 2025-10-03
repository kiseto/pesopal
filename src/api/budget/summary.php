<?php
session_start();

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
    
    // Get current month's date range
    $currentMonth = date('Y-m');
    $startOfMonth = $currentMonth . '-01';
    $endOfMonth = date('Y-m-t'); // Last day of current month
    
    // Calculate total budget allocated
    $budgetQuery = "SELECT SUM(allocated) as total_budget FROM budget_categories WHERE user_id = :user_id";
    $budgetStmt = $pdo->prepare($budgetQuery);
    $budgetStmt->bindParam(':user_id', $user_id);
    $budgetStmt->execute();
    $budgetResult = $budgetStmt->fetch(PDO::FETCH_ASSOC);
    $totalBudget = (float)($budgetResult['total_budget'] ?? 45000);
    
    // Calculate actual spent this month from transactions
    $spentQuery = "SELECT SUM(amount) as total_spent FROM transactions 
                   WHERE user_id = :user_id 
                   AND type = 'expense' 
                   AND DATE(date) BETWEEN :start_date AND :end_date";
    $spentStmt = $pdo->prepare($spentQuery);
    $spentStmt->bindParam(':user_id', $user_id);
    $spentStmt->bindParam(':start_date', $startOfMonth);
    $spentStmt->bindParam(':end_date', $endOfMonth);
    $spentStmt->execute();
    $spentResult = $spentStmt->fetch(PDO::FETCH_ASSOC);
    $totalSpent = (float)($spentResult['total_spent'] ?? 0);
    
    // Calculate total savings goal
    $savingsQuery = "SELECT SUM(target_amount) as total_target, SUM(saved_amount) as total_saved FROM savings_goals WHERE user_id = :user_id";
    $savingsStmt = $pdo->prepare($savingsQuery);
    $savingsStmt->bindParam(':user_id', $user_id);
    $savingsStmt->execute();
    $savingsResult = $savingsStmt->fetch(PDO::FETCH_ASSOC);
    $totalSavingsTarget = (float)($savingsResult['total_target'] ?? 120000);
    $totalSaved = (float)($savingsResult['total_saved'] ?? 0);
    
    // Calculate progress percentages
    $budgetProgress = $totalBudget > 0 ? min(($totalSpent / $totalBudget) * 100, 100) : 0;
    $savingsProgress = $totalSavingsTarget > 0 ? ($totalSaved / $totalSavingsTarget) * 100 : 0;
    
    // Calculate remaining budget
    $remaining = $totalBudget - $totalSpent;
    
    // Determine status
    $status = 'On Track';
    if ($remaining < 0) {
        $status = 'Over Budget';
    } elseif ($budgetProgress > 90) {
        $status = 'Near Limit';
    }
    
    // Get monthly data for chart (last 6 months)
    $monthlyData = [];
    for ($i = 5; $i >= 0; $i--) {
        $monthDate = date('Y-m', strtotime("-$i months"));
        $monthName = date('M', strtotime("-$i months"));
        $monthStart = $monthDate . '-01';
        $monthEnd = date('Y-m-t', strtotime($monthDate . '-01'));
        
        // Get spent for this month
        $monthSpentQuery = "SELECT SUM(amount) as month_spent FROM transactions 
                           WHERE user_id = :user_id 
                           AND type = 'expense' 
                           AND DATE(date) BETWEEN :start_date AND :end_date";
        $monthSpentStmt = $pdo->prepare($monthSpentQuery);
        $monthSpentStmt->bindParam(':user_id', $user_id);
        $monthSpentStmt->bindParam(':start_date', $monthStart);
        $monthSpentStmt->bindParam(':end_date', $monthEnd);
        $monthSpentStmt->execute();
        $monthSpentResult = $monthSpentStmt->fetch(PDO::FETCH_ASSOC);
        $monthSpent = (float)($monthSpentResult['month_spent'] ?? 0);
        
        $monthlyData[] = [
            'name' => $monthName,
            'budget' => $totalBudget, // Assume same budget each month
            'spent' => $monthSpent
        ];
    }
    
    $summary = [
        'monthlyBudget' => $totalBudget,
        'spentThisMonth' => $totalSpent,
        'progress' => round($budgetProgress, 1),
        'savingsGoal' => $totalSavingsTarget,
        'savedSoFar' => $totalSaved,
        'savingsProgress' => round($savingsProgress, 1),
        'remaining' => $remaining,
        'status' => $status,
        'monthlyData' => $monthlyData
    ];
    
    echo json_encode([
        'success' => true,
        'data' => $summary
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    http_response_code(500);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
}
?>