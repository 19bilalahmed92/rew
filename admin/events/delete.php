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
    // Get event data first to delete the image
    $stmt = $conn->prepare("SELECT poster_image FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Delete the event
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$id]);
    
    // Delete the poster image if exists
    if ($event && $event['poster_image'] && file_exists('../../uploads/events/' . $event['poster_image'])) {
        unlink('../../uploads/events/' . $event['poster_image']);
    }
    
    $_SESSION['success'] = "Event deleted successfully!";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error deleting event: " . $e->getMessage();
}

header("Location: index.php");
exit(); 