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
    
    // Get all invoices for the user
    $query = "SELECT 
                id,
                title,
                category,
                amount,
                due_date,
                status,
                notes,
                created_at
              FROM invoices 
              WHERE user_id = :user_id 
              ORDER BY due_date DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format invoices for the frontend
    $formatted_invoices = [];
    foreach ($invoices as $invoice) {
        $formatted_invoices[] = [
            'id' => $invoice['id'],
            'title' => $invoice['title'],
            'category' => $invoice['category'],
            'amount' => floatval($invoice['amount']),
            'dueDate' => $invoice['due_date'],
            'status' => $invoice['status'],
            'notes' => $invoice['notes'] ?? '',
            'receipt' => null, // Invoices don't have receipts
            'actions' => ''
        ];
    }
    
    echo json_encode(['success' => true, 'message' => 'Invoices retrieved successfully', 'data' => $formatted_invoices]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    http_response_code(500);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error retrieving invoices: ' . $e->getMessage()]);
    http_response_code(500);
}
?>