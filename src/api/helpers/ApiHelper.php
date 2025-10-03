<?php
// Common utility functions for API

class ApiHelper {
    
    // Validate required fields
    public static function validateRequiredFields($data, $required_fields) {
        $missing_fields = [];
        
        foreach ($required_fields as $field) {
            if (!isset($data->$field) || empty(trim($data->$field))) {
                $missing_fields[] = $field;
            }
        }
        
        return $missing_fields;
    }
    
    // Sanitize input data
    public static function sanitizeInput($data) {
        if (is_object($data)) {
            foreach ($data as $key => $value) {
                if (is_string($value)) {
                    $data->$key = htmlspecialchars(strip_tags(trim($value)));
                }
            }
        }
        return $data;
    }
    
    // Send JSON response
    public static function sendResponse($success, $message, $data = null, $status_code = 200) {
        http_response_code($status_code);
        
        $response = [
            'success' => $success,
            'message' => $message
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        echo json_encode($response);
        exit();
    }
    
    // Check if user is authenticated
    public static function requireAuth() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            self::sendResponse(false, 'Authentication required', null, 401);
        }
        
        return $_SESSION['user_id'];
    }
    
    // Validate email format
    public static function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    // Generate secure password hash
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
?>