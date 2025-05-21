<?php
require_once 'includes/config.php';

try {
    $conn = getDBConnection();
    echo "Database connection successful!<br>";
    
    // Test admin credentials
    $username = 'admin';
    $password = 'admin123';
    
    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($admin) {
        echo "Admin user found!<br>";
        echo "Stored password hash: " . $admin['password'] . "<br>";
        
        if(password_verify($password, $admin['password'])) {
            echo "Password verification successful!<br>";
        } else {
            echo "Password verification failed!<br>";
        }
    } else {
        echo "Admin user not found!<br>";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 