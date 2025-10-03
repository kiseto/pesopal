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
    $user_id = $_SESSION['user_id'] ?? 2;
    
    $pdo = new PDO("mysql:host=localhost;dbname=pesopal;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get budget categories
    $stmt = $pdo->prepare("SELECT * FROM budget_categories WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $formattedCategories = [];
    foreach ($categories as $cat) {
        $formattedCategories[] = [
            'id' => (int)$cat['id'],
            'title' => $cat['title'],
            'allocated' => (float)($cat['allocated_amount'] ?? 0),
            'spent' => (float)($cat['spent_amount'] ?? 0),
            'timeFrame' => $cat['time_frame'] ?? 'Monthly',
            'notes' => $cat['notes'] ?? '',
            'icon' => $cat['icon_class'] ?? 'bg-gray-100 text-gray-600 rounded-full p-1',
            'color' => $cat['color_class'] ?? 'bg-gray-500'
        ];
    }
    
    // Return empty array if no categories found (no sample data for fresh accounts)
    
    echo json_encode([
        'success' => true,
        'data' => $formattedCategories
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>