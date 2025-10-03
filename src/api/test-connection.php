<?php
require_once 'config/database.php';

// Test database connection
$database = new Database();
$db = $database->getConnection();

if ($db) {
    // Test if users table exists
    try {
        $stmt = $db->query("SELECT COUNT(*) FROM users");
        $count = $stmt->fetchColumn();
        
        echo json_encode([
            'success' => true,
            'message' => 'Database connection successful',
            'users_count' => $count
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database table error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
}
?>