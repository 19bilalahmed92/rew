<?php
require_once '../includes/config.php';
require_once 'auth_check.php';

// Get statistics
$conn = getDBConnection();

// Active wrestlers
$stmt = $conn->query("SELECT COUNT(*) FROM wrestlers WHERE status = 'active'");
$active_wrestlers = $stmt->fetchColumn();

// Inactive wrestlers
$stmt = $conn->query("SELECT COUNT(*) FROM wrestlers WHERE status = 'inactive'");
$inactive_wrestlers = $stmt->fetchColumn();

// Total wrestlers
$total_wrestlers = $active_wrestlers + $inactive_wrestlers;

// Upcoming events
$stmt = $conn->query("SELECT COUNT(*) FROM events WHERE status = 'upcoming'");
$upcoming_events = $stmt->fetchColumn();

// Total belts
$stmt = $conn->query("SELECT COUNT(*) FROM belts WHERE status = 'active'");
$total_belts = $stmt->fetchColumn();

// Recent events
$stmt = $conn->query("SELECT * FROM events WHERE status = 'upcoming' ORDER BY date ASC LIMIT 5");
$recent_events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recent wrestlers
$stmt = $conn->query("SELECT * FROM wrestlers WHERE status = 'active' ORDER BY created_at DESC LIMIT 5");
$recent_wrestlers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        .dashboard-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        .wrestler-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .wrestler-stat {
            text-align: center;
            padding: 5px 10px;
            border-radius: 5px;
            background: rgba(255,255,255,0.1);
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Admin Dashboard</h2>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card dashboard-card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Total Wrestlers</h6>
                                <h2 class="mb-0"><?php echo $total_wrestlers; ?></h2>
                                <div class="wrestler-stats">
                                    <div class="wrestler-stat">
                                        <small>Active</small>
                                        <div><?php echo $active_wrestlers; ?></div>
                                    </div>
                                    <div class="wrestler-stat">
                                        <small>Inactive</small>
                                        <div><?php echo $inactive_wrestlers; ?></div>
                                    </div>
                                </div>
                            </div>
                            <i class="fas fa-users stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Upcoming Events</h6>
                                <h2 class="mb-0"><?php echo $upcoming_events; ?></h2>
                            </div>
                            <i class="fas fa-calendar-alt stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Active Championships</h6>
                                <h2 class="mb-0"><?php echo $total_belts; ?></h2>
                            </div>
                            <i class="fas fa-trophy stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="wrestlers/add.php" class="btn btn-primary w-100">
                                    <i class="fas fa-user-plus me-2"></i>Add Wrestler
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="events/add.php" class="btn btn-success w-100">
                                    <i class="fas fa-calendar-plus me-2"></i>Add Event
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="belts/add.php" class="btn btn-warning w-100">
                                    <i class="fas fa-trophy me-2"></i>Add Championship
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="sliders/add.php" class="btn btn-info w-100">
                                    <i class="fas fa-images me-2"></i>Add Slider
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Events and Wrestlers -->
        <div class="row">
            <div class="col-md-6">
                <div class="card dashboard-card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Upcoming Events</h5>
                    </div>
                    <div class="card-body">
                        <?php if($recent_events): ?>
                            <div class="list-group">
                                <?php foreach($recent_events as $event): ?>
                                    <a href="events/edit.php?id=<?php echo $event['id']; ?>" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?php echo $event['name']; ?></h6>
                                            <small><?php echo date('M d, Y', strtotime($event['date'])); ?></small>
                                        </div>
                                        <small class="text-muted"><?php echo $event['location']; ?></small>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No upcoming events</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card dashboard-card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Recent Wrestlers</h5>
                    </div>
                    <div class="card-body">
                        <?php if($recent_wrestlers): ?>
                            <div class="list-group">
                                <?php foreach($recent_wrestlers as $wrestler): ?>
                                    <a href="wrestlers/edit.php?id=<?php echo $wrestler['id']; ?>" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?php echo $wrestler['name']; ?></h6>
                                            <small><?php echo date('M d, Y', strtotime($wrestler['created_at'])); ?></small>
                                        </div>
                                        <small class="text-muted"><?php echo $wrestler['real_name']; ?></small>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No wrestlers added yet</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
