<?php
require_once '../../includes/config.php';
require_once '../auth_check.php';

// Get all wrestlers
$conn = getDBConnection();
$stmt = $conn->query("SELECT * FROM wrestlers ORDER BY created_at DESC");
$wrestlers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Wrestlers - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .social-links a {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        .signature-moves {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Wrestlers</h2>
            <a href="add.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Wrestler
            </a>
        </div>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Real Name</th>
                                <th>Signature Moves</th>
                                <th>Social Media</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($wrestlers as $wrestler): ?>
                            <tr>
                                <td><?php echo $wrestler['id']; ?></td>
                                <td>
                                    <?php if($wrestler['image']): ?>
                                        <img src="../../uploads/wrestlers/<?php echo $wrestler['image']; ?>" 
                                             alt="<?php echo htmlspecialchars($wrestler['name']); ?>" 
                                             class="img-thumbnail" style="max-height: 50px;">
                                    <?php else: ?>
                                        <div class="bg-secondary text-white rounded" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($wrestler['name']); ?></td>
                                <td><?php echo htmlspecialchars($wrestler['real_name']); ?></td>
                                <td class="signature-moves" title="<?php echo htmlspecialchars($wrestler['signature_moves']); ?>">
                                    <?php echo htmlspecialchars($wrestler['signature_moves']); ?>
                                </td>
                                <td>
                                    <div class="social-links">
                                        <?php if($wrestler['twitter']): ?>
                                            <a href="https://twitter.com/<?php echo htmlspecialchars($wrestler['twitter']); ?>" target="_blank" class="text-info">
                                                <i class="fab fa-twitter"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if($wrestler['instagram']): ?>
                                            <a href="https://instagram.com/<?php echo htmlspecialchars($wrestler['instagram']); ?>" target="_blank" class="text-danger">
                                                <i class="fab fa-instagram"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if($wrestler['facebook']): ?>
                                            <a href="<?php echo htmlspecialchars($wrestler['facebook']); ?>" target="_blank" class="text-primary">
                                                <i class="fab fa-facebook"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $wrestler['status'] == 'active' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($wrestler['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($wrestler['updated_at'])); ?></td>
                                <td>
                                    <a href="edit.php?id=<?php echo $wrestler['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $wrestler['id']; ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this wrestler?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 