<?php
require_once '../../includes/config.php';
require_once '../auth_check.php';

$conn = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $real_name = trim($_POST['real_name']);
    $status = $_POST['status'];
    $bio = trim($_POST['bio']);
    $height = trim($_POST['height']);
    $weight = trim($_POST['weight']);
    $hometown = trim($_POST['hometown']);
    $signature_moves = trim($_POST['signature_moves']);
    $twitter = trim($_POST['twitter']);
    $instagram = trim($_POST['instagram']);
    $facebook = trim($_POST['facebook']);
    
    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = '../../uploads/wrestlers/' . $new_filename;
            
            if (!is_dir('../../uploads/wrestlers')) {
                mkdir('../../uploads/wrestlers', 0777, true);
            }
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image = $new_filename;
            }
        }
    }
    
    try {
        $stmt = $conn->prepare("INSERT INTO wrestlers (
            name, real_name, status, bio, height, weight, hometown, 
            signature_moves, image, twitter, instagram, facebook, 
            created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        
        $stmt->execute([
            $name, $real_name, $status, $bio, $height, $weight, $hometown,
            $signature_moves, $image, $twitter, $instagram, $facebook
        ]);
        
        $_SESSION['success'] = "Wrestler added successfully!";
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $error = "Error adding wrestler: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Wrestler - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Add New Wrestler</h2>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Ring Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="real_name" class="form-label">Real Name *</label>
                            <input type="text" class="form-control" id="real_name" name="real_name" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="height" class="form-label">Height</label>
                            <input type="text" class="form-control" id="height" name="height">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="weight" class="form-label">Weight</label>
                            <input type="text" class="form-control" id="weight" name="weight">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="hometown" class="form-label">Hometown</label>
                            <input type="text" class="form-control" id="hometown" name="hometown">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="bio" class="form-label">Biography</label>
                        <textarea class="form-control" id="bio" name="bio" rows="4"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="signature_moves" class="form-label">Signature Moves</label>
                        <textarea class="form-control" id="signature_moves" name="signature_moves" rows="2" 
                                placeholder="Enter signature moves separated by commas"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="twitter" class="form-label">Twitter Handle</label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="text" class="form-control" id="twitter" name="twitter">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="instagram" class="form-label">Instagram Handle</label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="text" class="form-control" id="instagram" name="instagram">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="facebook" class="form-label">Facebook URL</label>
                            <input type="url" class="form-control" id="facebook" name="facebook">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="image" class="form-label">Profile Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Wrestler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 