<?php
require_once 'includes/config.php';

try {
    $conn = getDBConnection();
    echo "Database connection successful!<br>";
    
    // Test query
    $stmt = $conn->query("SELECT COUNT(*) FROM wrestlers");
    $count = $stmt->fetchColumn();
    echo "Number of wrestlers in database: " . $count;
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?> 