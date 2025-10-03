<?php
session_start();

// Set JSON headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Handle preflight
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
    
    // Direct PDO connection
    $host = 'localhost';
    $dbname = 'pesopal';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get all receipts for the user
    $query = "SELECT 
                id,
                title,
                vendor,
                category,
                amount,
                receipt_date,
                file_path,
                notes,
                created_at
              FROM receipts 
              WHERE user_id = :user_id 
              ORDER BY receipt_date DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $receipts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format receipts for the frontend
    $formatted_receipts = [];
    foreach ($receipts as $receipt) {
        $formatted_receipts[] = [
            'id' => $receipt['id'],
            'title' => $receipt['title'],
            'category' => $receipt['category'],
            'amount' => floatval($receipt['amount']),
            'dueDate' => $receipt['receipt_date'], // Using receipt_date as dueDate for table compatibility
            'status' => 'Paid', // Receipts are always paid
            'notes' => $receipt['notes'] ?? '',
            'receipt' => $receipt['file_path'] ?? 'No file',
            'actions' => ''
        ];
    }
    
    echo json_encode(['success' => true, 'message' => 'Receipts retrieved successfully', 'data' => $formatted_receipts]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    http_response_code(500);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error retrieving receipts: ' . $e->getMessage()]);
    http_response_code(500);
}
?>