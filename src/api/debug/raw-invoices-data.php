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
    
    // Get exact invoice data from database
    $query = "SELECT id, user_id, title, category, amount, due_date, status, notes FROM invoices WHERE user_id = :user_id ORDER BY id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get exact receipt data from database  
    $receipt_query = "SELECT id, user_id, title, category, amount, receipt_date, file_path, notes FROM receipts WHERE user_id = :user_id ORDER BY id";
    $receipt_stmt = $pdo->prepare($receipt_query);
    $receipt_stmt->bindParam(':user_id', $user_id);
    $receipt_stmt->execute();
    $receipts = $receipt_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate summary manually
    $summary_totals = [
        'unpaid' => 0,
        'paid' => 0,
        'overdue' => 0,
        'upcoming' => 0
    ];
    
    foreach ($invoices as $invoice) {
        $status = strtolower($invoice['status']);
        $amount = floatval($invoice['amount']);
        if (isset($summary_totals[$status])) {
            $summary_totals[$status] += $amount;
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Raw database data retrieved',
        'data' => [
            'user_id' => $user_id,
            'raw_invoices' => $invoices,
            'raw_receipts' => $receipts,
            'manual_summary' => [
                'total_due' => $summary_totals['unpaid'],
                'paid' => $summary_totals['paid'], 
                'overdue' => $summary_totals['overdue'],
                'upcoming' => $summary_totals['upcoming']
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    http_response_code(500);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
}
?>