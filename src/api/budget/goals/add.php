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
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['title']) || !isset($input['target'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        http_response_code(400);
        exit;
    }
    
    $host = 'localhost';
    $dbname = 'pesopal';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Insert new savings goal
    $query = "INSERT INTO savings_goals (user_id, title, target_amount, target_date, notes) VALUES (:user_id, :title, :target_amount, :target_date, :notes)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':title', $input['title']);
    $stmt->bindParam(':target_amount', $input['target']);
    $stmt->bindParam(':target_date', $input['targetDate'] ?? null);
    $stmt->bindParam(':notes', $input['notes'] ?? '');
    
    if ($stmt->execute()) {
        $goal_id = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Savings goal added successfully',
            'data' => [
                'id' => $goal_id,
                'title' => $input['title'],
                'target' => (float)$input['target'],
                'saved' => 0,
                'targetDate' => $input['targetDate'] ?? null,
                'icon' => 'bg-gray-100 text-gray-600 rounded-full p-1',
                'color' => '#64748b',
                'notes' => $input['notes'] ?? ''
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add savings goal']);
        http_response_code(500);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    http_response_code(500);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
}
?>