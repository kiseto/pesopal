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
    
    if (!$input || !isset($input['title']) || !isset($input['allocated'])) {
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
    
    // Insert new budget category
    $query = "INSERT INTO budget_categories (user_id, title, allocated, time_frame, notes) VALUES (:user_id, :title, :allocated, :time_frame, :notes)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':title', $input['title']);
    $stmt->bindParam(':allocated', $input['allocated']);
    $stmt->bindParam(':time_frame', $input['timeFrame'] ?? 'Monthly');
    $stmt->bindParam(':notes', $input['notes'] ?? '');
    
    if ($stmt->execute()) {
        $category_id = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Budget category added successfully',
            'data' => [
                'id' => $category_id,
                'title' => $input['title'],
                'allocated' => (float)$input['allocated'],
                'spent' => 0,
                'icon' => 'bg-gray-100 text-gray-600 rounded-full p-1',
                'color' => 'bg-gray-500',
                'timeFrame' => $input['timeFrame'] ?? 'Monthly',
                'notes' => $input['notes'] ?? ''
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add budget category']);
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