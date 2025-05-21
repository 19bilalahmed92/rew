<?php
//session_start();
//if (!isset($_SESSION['admin_logged_in'])) {
    //header('Location: login.php');
  //  exit;
//}

require_once 'includes/config.php';

// Get search parameters
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';

// Build query
$query = "SELECT * FROM belts WHERE status = 'active'";
$params = [];

if($search) {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param]);
}

$query .= " ORDER BY name ASC";

// Get belts
$conn = getDBConnection();
$stmt = $conn->prepare($query);
$stmt->execute($params);
$belts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get single belt if ID is provided
$belt = null;
if(isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM belts WHERE id = ? AND status = 'active'");
    $stmt->execute([$id]);
    $belt = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($belt) {
        // Get title history
        $stmt = $conn->prepare("
            SELECT th.*, w.name as wrestler_name, w.image as wrestler_image
            FROM title_history th
            JOIN wrestlers w ON th.wrestler_id = w.id
            WHERE th.belt_id = ?
            ORDER BY th.won_date DESC
        ");
        $stmt->execute([$id]);
        $title_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get current champion
        $stmt = $conn->prepare("
            SELECT w.*
            FROM title_history th
            JOIN wrestlers w ON th.wrestler_id = w.id
            WHERE th.belt_id = ?
            AND (th.lost_date IS NULL OR th.lost_date > CURRENT_TIMESTAMP)
            ORDER BY th.won_date DESC
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $current_champion = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<?php include 'includes/header.php'; ?>

<?php if($belt): ?>
    <!-- Single Championship Details -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <?php if($belt['image']): ?>
                    <img src="<?php echo SITE_URL; ?>/assets/uploads/belts/<?php echo $belt['image']; ?>" 
                         class="img-fluid rounded mb-4" alt="<?php echo $belt['name']; ?>">
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Championship Information</h5>
                        <p class="card-text"><?php echo nl2br($belt['description']); ?></p>
                        
                        <?php if($current_champion): ?>
                            <div class="mt-4">
                                <h6>Current Champion</h6>
                                <div class="d-flex align-items-center">
                                    <?php if($current_champion['image']): ?>
                                        <img src="<?php echo SITE_URL; ?>/assets/uploads/wrestlers/<?php echo $current_champion['image']; ?>" 
                                             class="rounded-circle me-3" width="50" height="50" 
                                             alt="<?php echo $current_champion['name']; ?>">
                                    <?php endif; ?>
                                    <div>
                                        <h6 class="mb-0"><?php echo $current_champion['name']; ?></h6>
                                        <a href="wrestlers.php?id=<?php echo $current_champion['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary mt-2">View Profile</a>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="mt-4">
                                <h6>Current Status</h6>
                                <span class="badge bg-secondary">Vacant</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <h1><?php echo $belt['name']; ?></h1>
                
                <?php if($title_history): ?>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Title History</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Champion</th>
                                            <th>Won</th>
                                            <th>Lost</th>
                                            <th>Reign</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($title_history as $title): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if($title['wrestler_image']): ?>
                                                            <img src="<?php echo SITE_URL; ?>/assets/uploads/wrestlers/<?php echo $title['wrestler_image']; ?>" 
                                                                 class="rounded-circle me-2" width="30" height="30" 
                                                                 alt="<?php echo $title['wrestler_name']; ?>">
                                                        <?php endif; ?>
                                                        <a href="wrestlers.php?id=<?php echo $title['wrestler_id']; ?>">
                                                            <?php echo $title['wrestler_name']; ?>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($title['won_date'])); ?></td>
                                                <td><?php echo $title['lost_date'] ? date('M d, Y', strtotime($title['lost_date'])) : 'Current'; ?></td>
                                                <td><?php echo $title['reign_number']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        No title history available for this championship.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Championships Listing -->
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2>Championships</h2>
            </div>
            <div class="col-md-6">
                <form action="" method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" 
                           placeholder="Search championships..." value="<?php echo $search; ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
        </div>
        
        <div class="row">
            <?php foreach($belts as $belt): ?>
                <div class="col-md-4 mb-4">
                    <div class="card belt-card h-100">
                        <?php if($belt['image']): ?>
                            <img src="<?php echo SITE_URL; ?>/assets/uploads/belts/<?php echo $belt['image']; ?>" 
                                 class="card-img-top" alt="<?php echo $belt['name']; ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $belt['name']; ?></h5>
                            <p class="card-text"><?php echo substr($belt['description'], 0, 100); ?>...</p>
                            <a href="?id=<?php echo $belt['id']; ?>" class="btn btn-primary">View History</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
