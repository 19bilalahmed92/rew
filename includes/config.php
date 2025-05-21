<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'rew_wrestling');

// Site configuration
define('SITE_NAME', 'REW Wrestling');
define('SITE_URL', 'http://localhost/rew_pakistan');
define('ADMIN_EMAIL', 'admin@rew-wrestling.com');

// File upload paths
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/rew_pakistan/assets/uploads/');
define('WRESTLER_IMAGES', UPLOAD_PATH . 'wrestlers/');
define('EVENT_IMAGES', UPLOAD_PATH . 'events/');
define('BELT_IMAGES', UPLOAD_PATH . 'belts/');
define('SLIDER_IMAGES', UPLOAD_PATH . 'sliders/');

// Create database connection
function getDBConnection() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );
        return $conn;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Error handling
function handleError($errno, $errstr, $errfile, $errline) {
    $error = "Error [$errno] $errstr - $errfile:$errline";
    error_log($error);
    if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE) {
        echo $error;
    }
}

set_error_handler("handleError");

// Session configuration
session_start();

// Security functions
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }
    return true;
}
?> 