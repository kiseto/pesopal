<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    error_log('=== UPDATE BUDGET CATEGORY SPENT ===');
    error_log('Input: ' . json_encode($input));
    error_log('User ID: ' . $_SESSION['user_id']);
    
    if (!isset($input['id']) || !isset($input['amount'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }
    
    $categoryId = $input['id'];
    $amount = floatval($input['amount']);
    $userId = $_SESSION['user_id'];
    
    error_log("Updating category ID: $categoryId with amount: $amount for user: $userId");
    
    // PDO connection like other budget endpoints
    $pdo = new PDO("mysql:host=localhost;dbname=pesopal;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Update the spent amount for the budget category
    $stmt = $pdo->prepare("UPDATE budget_categories SET spent_amount = spent_amount + ? WHERE id = ? AND user_id = ?");
    $result = $stmt->execute([$amount, $categoryId, $userId]);
    
    if ($result) {
        $affectedRows = $stmt->rowCount();
        error_log("SQL executed successfully. Affected rows: $affectedRows");
        
        if ($affectedRows > 0) {
            echo json_encode(['success' => true, 'message' => 'Budget category spent amount updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No matching budget category found']);
        }
    } else {
        error_log("SQL execution failed");
        echo json_encode(['success' => false, 'message' => 'Failed to update budget category']);
    }
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
