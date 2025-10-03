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
    
    // Validate required fields from POST data
    if (!isset($_POST['title']) || !isset($_POST['category']) || 
        !isset($_POST['amount']) || !isset($_POST['receipt_date'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        http_response_code(400);
        exit;
    }
    
    // Handle file upload
    $uploadedFileName = null;
    if (isset($_FILES['receipt_file']) && $_FILES['receipt_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../../uploads/receipts/';
        
        // Create upload directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileInfo = pathinfo($_FILES['receipt_file']['name']);
        $fileName = $user_id . '_' . time() . '_' . uniqid() . '.' . $fileInfo['extension'];
        $uploadPath = $uploadDir . $fileName;
        
        // Validate file type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        if (!in_array(strtolower($fileInfo['extension']), $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and PDF files are allowed.']);
            http_response_code(400);
            exit;
        }
        
        // Validate file size (5MB max)
        if ($_FILES['receipt_file']['size'] > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 5MB.']);
            http_response_code(400);
            exit;
        }
        
        if (move_uploaded_file($_FILES['receipt_file']['tmp_name'], $uploadPath)) {
            $uploadedFileName = $fileName;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
            http_response_code(500);
            exit;
        }
    }
    
    // Direct PDO connection
    $host = 'localhost';
    $dbname = 'pesopal';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Insert the new receipt
    $query = "INSERT INTO receipts (user_id, title, category, amount, receipt_date, notes, receipt_file) 
              VALUES (:user_id, :title, :category, :amount, :receipt_date, :notes, :receipt_file)";
              
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':title', $_POST['title']);
    $stmt->bindParam(':category', $_POST['category']);
    $stmt->bindParam(':amount', $_POST['amount']);
    $stmt->bindParam(':receipt_date', $_POST['receipt_date']);
    $stmt->bindParam(':notes', $_POST['notes']);
    $stmt->bindParam(':receipt_file', $uploadedFileName);
    
    $result = $stmt->execute();
    
    if ($result) {
        $receiptId = $pdo->lastInsertId();
        
        // Return the new receipt data
        echo json_encode([
            'success' => true, 
            'message' => 'Receipt added successfully',
            'data' => [
                'id' => (int)$receiptId,
                'title' => $_POST['title'],
                'category' => $_POST['category'],
                'amount' => (float)$_POST['amount'],
                'date' => $_POST['receipt_date'],
                'receipt' => $uploadedFileName ? $uploadedFileName : 'No file',
                'notes' => $_POST['notes'],
                'actions' => ''
            ]
        ]);
    } else {
        // If database insert failed but file was uploaded, delete the file
        if ($uploadedFileName && file_exists($uploadDir . $uploadedFileName)) {
            unlink($uploadDir . $uploadedFileName);
        }
        
        echo json_encode(['success' => false, 'message' => 'Failed to add receipt']);
        http_response_code(500);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
}
?>