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
    
    // Calculate number of months to show based on range
    $months_to_show = $range_days <= 30 ? 1 : ($range_days <= 90 ? 3 : 12);
    
    // Get monthly spending data for the specified range
    $monthlySpending = [];
    $monthlyLabels = [];
    
    // Get spending for each month within the range
    for ($i = $months_to_show - 1; $i >= 0; $i--) {
        $date = date('Y-m', strtotime("-$i months"));
        $monthName = date('M', strtotime("-$i months")); // Jan, Feb, etc.
        
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as monthly_expense 
            FROM transactions 
            WHERE user_id = ? 
            AND type = 'expense' 
            AND DATE_FORMAT(created_at, '%Y-%m') = ?
        ");
        $stmt->execute([$user_id, $date]);
        $monthlyExpense = (float)$stmt->fetchColumn();
        
        $monthlyLabels[] = $monthName;
        $monthlySpending[] = $monthlyExpense;
    }

    // Clear any potential output and send JSON
    ob_clean();
    echo json_encode([
        'success' => true,
        'data' => [
            'labels' => $monthlyLabels,
            'spending' => $monthlySpending,
            'debug' => [
                'user_id' => $user_id,
                'session_exists' => isset($_SESSION['user_id']),
                'range_days' => $range_days,
                'months_shown' => $months_to_show,
                'monthly_data' => array_combine($monthlyLabels, $monthlySpending)
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
