<?php
require_once '../../includes/config.php';
require_once '../auth_check.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$conn = getDBConnection();

// Get event data
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    
    // Handle file upload
    $poster_image = $event['poster_image']; // Keep existing image by default
    if (isset($_FILES['poster_image']) && $_FILES['poster_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['poster_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = '../../uploads/events/' . $new_filename;
            
            if (move_uploaded_file($_FILES['poster_image']['tmp_name'], $upload_path)) {
                // Delete old image if exists
                if ($event['poster_image'] && file_exists('../../uploads/events/' . $event['poster_image'])) {
                    unlink('../../uploads/events/' . $event['poster_image']);
                }
                $poster_image = $new_filename;
            }
        }
    }
    
    try {
        $stmt = $conn->prepare("UPDATE events SET 
                               name = ?, 
                               date = ?, 
                               location = ?, 
                               description = ?, 
                               poster_image = ?, 
                               status = ?,
                               updated_at = NOW()
                               WHERE id = ?");
        $stmt->execute([$name, $date, $location, $description, $poster_image, $status, $id]);
        
        $_SESSION['success'] = "Event updated successfully!";
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $error = "Error updating event: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Event</h2>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Events
            </a>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Event Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo htmlspecialchars($event['name']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Event Date</label>
                        <input type="datetime-local" class="form-control" id="date" name="date" 
                               value="<?php echo date('Y-m-d\TH:i', strtotime($event['date'])); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" 
                               value="<?php echo htmlspecialchars($event['location']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="4" required><?php echo htmlspecialchars($event['description']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="poster_image" class="form-label">Poster Image</label>
                        <?php if($event['poster_image']): ?>
                            <div class="mb-2">
                                <img src="../../uploads/events/<?php echo $event['poster_image']; ?>" 
                                     alt="Current poster" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="poster_image" name="poster_image" accept="image/*">
                        <small class="text-muted">Leave empty to keep current image. Allowed formats: JPG, JPEG, PNG, GIF</small>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="upcoming" <?php echo $event['status'] == 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                            <option value="completed" <?php echo $event['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo $event['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Event
                    </button>
                </form>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 