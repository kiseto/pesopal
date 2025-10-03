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
    
    // Get savings goals for the user
    $query = "SELECT id, title, target_amount, saved_amount, target_date, icon_class, color, notes FROM savings_goals WHERE user_id = :user_id ORDER BY id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    $goals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format goals for frontend
    $formattedGoals = [];
    foreach ($goals as $goal) {
        $formattedGoals[] = [
            'id' => (int)$goal['id'],
            'title' => $goal['title'],
            'target' => (float)$goal['target_amount'],
            'saved' => (float)$goal['saved_amount'],
            'targetDate' => $goal['target_date'],
            'icon' => $goal['icon_class'],
            'color' => $goal['color'],
            'notes' => $goal['notes']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $formattedGoals
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    http_response_code(500);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
}
?>