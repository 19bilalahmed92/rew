<?php
// Check if user is logged in
if(!isset($_SESSION['admin_id'])) {
    $_SESSION['message'] = 'Please login to access the admin panel';
    $_SESSION['message_type'] = 'danger';
    header('Location: login.php');
    exit();
}

// Get admin information
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM admins WHERE id = ? AND status = 'active'");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$admin) {
    session_destroy();
    $_SESSION['message'] = 'Your account has been deactivated';
    $_SESSION['message_type'] = 'danger';
    header('Location: login.php');
    exit();
}
?> 