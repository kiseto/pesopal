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
    
    // Get category spending for the specified date range
    $stmt = $pdo->prepare("
        SELECT 
            bc.title,
            COALESCE(SUM(t.amount), 0) as spent_amount
        FROM budget_categories bc
        LEFT JOIN transactions t ON bc.id = t.budget_category_id 
            AND t.user_id = ? 
            AND t.type = 'expense'
            AND t.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        WHERE bc.user_id = ?
        GROUP BY bc.id, bc.title
        ORDER BY spent_amount DESC
    ");
    $stmt->execute([$user_id, $range_days, $user_id]);
    $categoryData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate total spending for percentage calculation
    $totalSpent = array_sum(array_column($categoryData, 'spent_amount'));
    
    // Format the data with percentages and colors
    $colors = ['bg-green-400', 'bg-blue-400', 'bg-purple-400', 'bg-yellow-400', 'bg-red-400', 'bg-indigo-400', 'bg-pink-400', 'bg-orange-400'];
    $formattedCategories = [];
    
    foreach ($categoryData as $index => $category) {
        $amount = (float)$category['spent_amount'];
        $percent = $totalSpent > 0 ? round(($amount / $totalSpent) * 100, 1) : 0;
        
        $formattedCategories[] = [
            'title' => $category['title'],
            'amount' => $amount,
            'percent' => $percent,
            'color' => $colors[$index % count($colors)]
        ];
    }

    // Clear any potential output and send JSON
    ob_clean();
    echo json_encode([
        'success' => true,
        'data' => $formattedCategories,
        'debug' => [
            'user_id' => $user_id,
            'session_exists' => isset($_SESSION['user_id']),
            'range_days' => $range_days,
            'total_spent' => $totalSpent,
            'categories_count' => count($formattedCategories)
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
