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

try {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'User not authenticated']);
        http_response_code(401);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data or missing goal ID']);
        http_response_code(400);
        exit;
    }
    
    $host = 'localhost';
    $dbname = 'pesopal';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verify the goal belongs to the user
    $checkStmt = $pdo->prepare("SELECT id FROM savings_goals WHERE id = :id AND user_id = :user_id");
    $checkStmt->bindParam(':id', $input['id']);
    $checkStmt->bindParam(':user_id', $user_id);
    $checkStmt->execute();
    
    if (!$checkStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Savings goal not found or access denied']);
        http_response_code(404);
        exit;
    }
    
    // Update the goal
    $updateFields = [];
    $params = [':id' => $input['id'], ':user_id' => $user_id];
    
    if (isset($input['title'])) {
        $updateFields[] = 'title = :title';
        $params[':title'] = $input['title'];
    }
    
    if (isset($input['target_amount'])) {
        $updateFields[] = 'target_amount = :target_amount';
        $params[':target_amount'] = floatval($input['target_amount']);
    }
    
    if (isset($input['saved_amount'])) {
        $updateFields[] = 'saved_amount = :saved_amount';
        $params[':saved_amount'] = floatval($input['saved_amount']);
    }
    
    if (isset($input['target_date'])) {
        $updateFields[] = 'target_date = :target_date';
        $params[':target_date'] = $input['target_date'];
    }
    
    if (isset($input['notes'])) {
        $updateFields[] = 'notes = :notes';
        $params[':notes'] = $input['notes'];
    }
    
    if (isset($input['icon_class'])) {
        $updateFields[] = 'icon_class = :icon_class';
        $params[':icon_class'] = $input['icon_class'];
    }
    
    if (isset($input['color'])) {
        $updateFields[] = 'color = :color';
        $params[':color'] = $input['color'];
    }
    
    if (empty($updateFields)) {
        echo json_encode(['success' => false, 'message' => 'No fields to update']);
        http_response_code(400);
        exit;
    }
    
    $sql = "UPDATE savings_goals SET " . implode(', ', $updateFields) . " WHERE id = :id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Savings goal updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made to savings goal']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    http_response_code(500);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
}
?>