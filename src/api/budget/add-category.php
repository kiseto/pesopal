<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $user_id = $_SESSION['user_id'] ?? 2;
    $input = json_decode(file_get_contents('php://input'), true);
    
    $pdo = new PDO("mysql:host=localhost;dbname=pesopal;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("INSERT INTO budget_categories (user_id, title, allocated_amount, time_frame, notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $user_id,
        $input['title'],
        $input['allocated_amount'],
        $input['time_frame'] ?? 'Monthly',
        $input['notes'] ?? ''
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Budget category added successfully',
        'id' => $pdo->lastInsertId()
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>