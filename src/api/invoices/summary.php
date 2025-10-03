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
    
    // Get invoice summary by status
    $summary_query = "SELECT 
                        status,
                        COALESCE(SUM(amount), 0) as total_amount,
                        COUNT(*) as count
                      FROM invoices 
                      WHERE user_id = :user_id 
                      GROUP BY status";
    
    $stmt = $pdo->prepare($summary_query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $summary_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Initialize summary with default values
    $summary = [
        'total_due' => 0,
        'paid' => 0,
        'overdue' => 0,
        'upcoming' => 0
    ];
    
    // Calculate totals based on status
    foreach ($summary_results as $result) {
        $status = strtolower($result['status']);
        $amount = floatval($result['total_amount']);
        
        switch ($status) {
            case 'unpaid':
                $summary['total_due'] += $amount;
                break;
            case 'paid':
                $summary['paid'] += $amount;
                break;
            case 'overdue':
                $summary['overdue'] += $amount;
                break;
            case 'upcoming':
                $summary['upcoming'] += $amount;
                break;
        }
    }
    
    echo json_encode(['success' => true, 'message' => 'Summary retrieved successfully', 'data' => $summary]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    http_response_code(500);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error retrieving summary: ' . $e->getMessage()]);
    http_response_code(500);
}
?>