<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';
require_once '../models/User.php';
require_once '../helpers/ApiHelper.php';

// Require authentication
$user_id = ApiHelper::requireAuth();

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize User object
$user = new User($db);

// Get user data
if ($user->getUserById($user_id)) {
    ApiHelper::sendResponse(true, 'User profile retrieved successfully', [
        'user' => [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'birthday' => $user->birthday,
            'full_name' => $user->first_name . ' ' . $user->last_name
        ]
    ]);
} else {
    ApiHelper::sendResponse(false, 'User not found', null, 404);
}
?>