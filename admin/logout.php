<?php
require_once '../includes/config.php';

// Destroy session
session_destroy();

// Set message
session_start();
$_SESSION['message'] = 'You have been successfully logged out';
$_SESSION['message_type'] = 'success';

// Redirect to login page
header('Location: login.php');
exit();
?>
