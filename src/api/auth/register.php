<?php
require_once '../config/database.php';
require_once '../models/User.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize User object
$user = new User($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (empty($data->first_name) || empty($data->last_name) || empty($data->email) || empty($data->password)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Please fill in all required fields (first name, last name, email, password)'
    ]);
    exit();
}

// Validate email format
if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Please provide a valid email address'
    ]);
    exit();
}

// Validate password strength (minimum 6 characters)
if (strlen($data->password) < 6) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Password must be at least 6 characters long'
    ]);
    exit();
}

// Check if passwords match (if confirm password is provided)
if (isset($data->confirm_password) && $data->password !== $data->confirm_password) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Passwords do not match'
    ]);
    exit();
}

// Set user properties
$user->email = $data->email;

// Check if user already exists
if ($user->emailExists()) {
    http_response_code(409);
    echo json_encode([
        'success' => false,
        'message' => 'User with this email already exists'
    ]);
    exit();
}

// Set user data
$user->first_name = $data->first_name;
$user->last_name = $data->last_name;
$user->email = $data->email;
$user->phone = isset($data->phone) ? $data->phone : null;
$user->birthday = isset($data->birthday) ? $data->birthday : null;
$user->password_hash = password_hash($data->password, PASSWORD_DEFAULT);

// Create user
if ($user->create()) {
    // Create default user settings
    try {
        $settings_query = "INSERT INTO user_settings (user_id, monthly_budget, currency) VALUES (:user_id, 0.00, 'PHP')";
        $settings_stmt = $db->prepare($settings_query);
        $settings_stmt->bindParam(':user_id', $user->id);
        $settings_stmt->execute();
    } catch (Exception $e) {
        // Settings creation failed, but user was created successfully
        error_log("Failed to create user settings: " . $e->getMessage());
    }

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'User registration successful',
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
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'User registration failed. Please try again.'
    ]);
}
?>