<?php
require_once '../config/database.php';
require_once '../models/User.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Not authenticated'
    ]);
    exit();
}

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize User object
$user = new User($db);

// Get user data
if ($user->getUserById($_SESSION['user_id'])) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'birthday' => $user->birthday
        ]
    ]);
} else {
    // User not found, destroy session
    session_unset();
    session_destroy();
    
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'User session invalid'
    ]);
}
?>