<?php
session_start();

// Set JSON headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

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
    
    // Check if user already has invoices
    $check_invoices = $pdo->prepare("SELECT COUNT(*) as count FROM invoices WHERE user_id = :user_id");
    $check_invoices->bindParam(':user_id', $user_id);
    $check_invoices->execute();
    $existing_invoices = $check_invoices->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Check if user already has receipts
    $check_receipts = $pdo->prepare("SELECT COUNT(*) as count FROM receipts WHERE user_id = :user_id");
    $check_receipts->bindParam(':user_id', $user_id);
    $check_receipts->execute();
    $existing_receipts = $check_receipts->fetch(PDO::FETCH_ASSOC)['count'];
    
    $created_invoices = 0;
    $created_receipts = 0;
    
    // Create sample invoices if none exist
    if ($existing_invoices == 0) {
        $sample_invoices = [
            ['title' => 'Electric Bill - September', 'category' => 'Utilities', 'amount' => 1500.00, 'due_date' => '2025-10-15', 'status' => 'Unpaid'],
            ['title' => 'Internet Bill', 'category' => 'Utilities', 'amount' => 2500.00, 'due_date' => '2025-10-20', 'status' => 'Unpaid'],
            ['title' => 'Apartment Rent', 'category' => 'Rent', 'amount' => 15000.00, 'due_date' => '2025-10-01', 'status' => 'Paid']
        ];
        
        $invoice_query = "INSERT INTO invoices (user_id, title, category, amount, due_date, status) VALUES (:user_id, :title, :category, :amount, :due_date, :status)";
        $invoice_stmt = $pdo->prepare($invoice_query);
        
        foreach ($sample_invoices as $invoice) {
            $invoice_stmt->bindParam(':user_id', $user_id);
            $invoice_stmt->bindParam(':title', $invoice['title']);
            $invoice_stmt->bindParam(':category', $invoice['category']);
            $invoice_stmt->bindParam(':amount', $invoice['amount']);
            $invoice_stmt->bindParam(':due_date', $invoice['due_date']);
            $invoice_stmt->bindParam(':status', $invoice['status']);
            
            if ($invoice_stmt->execute()) {
                $created_invoices++;
            }
        }
    }
    
    // Create sample receipts if none exist
    if ($existing_receipts == 0) {
        $sample_receipts = [
            ['title' => 'Grocery Shopping', 'vendor' => 'SM Supermarket', 'category' => 'Food', 'amount' => 2200.00, 'receipt_date' => '2025-09-20'],
            ['title' => 'Gas Fill-up', 'vendor' => 'Shell Station', 'category' => 'Transport', 'amount' => 1200.00, 'receipt_date' => '2025-09-18'],
            ['title' => 'Medical Checkup', 'vendor' => 'Metro Hospital', 'category' => 'Health', 'amount' => 3500.00, 'receipt_date' => '2025-09-15']
        ];
        
        $receipt_query = "INSERT INTO receipts (user_id, title, vendor, category, amount, receipt_date) VALUES (:user_id, :title, :vendor, :category, :amount, :receipt_date)";
        $receipt_stmt = $pdo->prepare($receipt_query);
        
        foreach ($sample_receipts as $receipt) {
            $receipt_stmt->bindParam(':user_id', $user_id);
            $receipt_stmt->bindParam(':title', $receipt['title']);
            $receipt_stmt->bindParam(':vendor', $receipt['vendor']);
            $receipt_stmt->bindParam(':category', $receipt['category']);
            $receipt_stmt->bindParam(':amount', $receipt['amount']);
            $receipt_stmt->bindParam(':receipt_date', $receipt['receipt_date']);
            
            if ($receipt_stmt->execute()) {
                $created_receipts++;
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Sample data creation completed',
        'data' => [
            'user_id' => $user_id,
            'existing_invoices' => $existing_invoices,
            'created_invoices' => $created_invoices,
            'existing_receipts' => $existing_receipts,
            'created_receipts' => $created_receipts
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