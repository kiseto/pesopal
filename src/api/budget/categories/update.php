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
        echo json_encode(['success' => false, 'message' => 'Invalid input data or missing category ID']);
        http_response_code(400);
        exit;
    }
    
    $host = 'localhost';
    $dbname = 'pesopal';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verify the category belongs to the user
    $checkStmt = $pdo->prepare("SELECT id FROM budget_categories WHERE id = :id AND user_id = :user_id");
    $checkStmt->bindParam(':id', $input['id']);
    $checkStmt->bindParam(':user_id', $user_id);
    $checkStmt->execute();
    
    if (!$checkStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Budget category not found or access denied']);
        http_response_code(404);
        exit;
    }
    
    // Update the category
    $updateFields = [];
    $params = [':id' => $input['id'], ':user_id' => $user_id];
    
    if (isset($input['title'])) {
        $updateFields[] = 'title = :title';
        $params[':title'] = $input['title'];
    }
    
    if (isset($input['allocated'])) {
        $updateFields[] = 'allocated = :allocated';
        $params[':allocated'] = floatval($input['allocated']);
    }
    
    if (isset($input['spent'])) {
        $updateFields[] = 'spent = :spent';
        $params[':spent'] = floatval($input['spent']);
    }
    
    if (isset($input['time_frame'])) {
        $updateFields[] = 'time_frame = :time_frame';
        $params[':time_frame'] = $input['time_frame'];
    }
    
    if (isset($input['notes'])) {
        $updateFields[] = 'notes = :notes';
        $params[':notes'] = $input['notes'];
    }
    
    if (isset($input['icon_class'])) {
        $updateFields[] = 'icon_class = :icon_class';
        $params[':icon_class'] = $input['icon_class'];
    }
    
    if (isset($input['color_class'])) {
        $updateFields[] = 'color_class = :color_class';
        $params[':color_class'] = $input['color_class'];
    }
    
    if (empty($updateFields)) {
        echo json_encode(['success' => false, 'message' => 'No fields to update']);
        http_response_code(400);
        exit;
    }
    
    $sql = "UPDATE budget_categories SET " . implode(', ', $updateFields) . " WHERE id = :id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Budget category updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made to budget category']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    http_response_code(500);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
}
?>