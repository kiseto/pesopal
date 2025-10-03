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
    
    // Get budget categories for the user
    $query = "SELECT id, title, allocated, spent, time_frame, icon_class, color_class, notes FROM budget_categories WHERE user_id = :user_id ORDER BY id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format categories for frontend
    $formattedCategories = [];
    foreach ($categories as $cat) {
        $formattedCategories[] = [
            'id' => (int)$cat['id'],
            'title' => $cat['title'],
            'allocated' => (float)$cat['allocated'],
            'spent' => (float)$cat['spent'],
            'icon' => $cat['icon_class'],
            'color' => $cat['color_class'],
            'timeFrame' => $cat['time_frame'],
            'notes' => $cat['notes']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $formattedCategories
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    http_response_code(500);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
}
?>