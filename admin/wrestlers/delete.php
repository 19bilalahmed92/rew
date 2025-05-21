<?php
require_once '../../includes/config.php';
require_once '../auth_check.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$conn = getDBConnection();

try {
    // Get wrestler data first to delete image
    $stmt = $conn->prepare("SELECT image FROM wrestlers WHERE id = ?");
    $stmt->execute([$id]);
    $wrestler = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Delete the wrestler
    $stmt = $conn->prepare("DELETE FROM wrestlers WHERE id = ?");
    $stmt->execute([$id]);
    
    // Delete the image if exists
    if ($wrestler && $wrestler['image'] && file_exists('../../uploads/wrestlers/' . $wrestler['image'])) {
        unlink('../../uploads/wrestlers/' . $wrestler['image']);
    }
    
    $_SESSION['success'] = "Wrestler deleted successfully!";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error deleting wrestler: " . $e->getMessage();
}

header("Location: index.php");
exit(); 