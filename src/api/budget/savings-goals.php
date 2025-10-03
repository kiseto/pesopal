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
    
    // Get savings goals
    $stmt = $pdo->prepare("SELECT * FROM savings_goals WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $goals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $formattedGoals = [];
    foreach ($goals as $goal) {
        $formattedGoals[] = [
            'id' => (int)$goal['id'],
            'title' => $goal['title'],
            'target' => (float)$goal['target_amount'],
            'saved' => (float)$goal['saved_amount'],
            'targetDate' => $goal['target_date'],
            'color' => $goal['color'] ?? '#2563eb',
            'icon' => $goal['icon_class'] ?? 'bg-blue-100 text-blue-600 rounded-full p-1',
            'notes' => $goal['notes'] ?? '',
            'status' => $goal['status'] ?? 'active'
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $formattedGoals
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>