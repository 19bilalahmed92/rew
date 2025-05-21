<?php
require_once 'includes/config.php';

// Get search parameters
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$sort = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'name';
$order = isset($_GET['order']) ? sanitizeInput($_GET['order']) : 'ASC';

// Build query
$query = "SELECT * FROM wrestlers WHERE status = 'active'";
$params = [];

if($search) {
    $query .= " AND (name LIKE ? OR real_name LIKE ? OR bio LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
}

$query .= " ORDER BY $sort $order";

// Get wrestlers
$conn = getDBConnection();
$stmt = $conn->prepare($query);
$stmt->execute($params);
$wrestlers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get single wrestler if ID is provided
$wrestler = null;
if(isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM wrestlers WHERE id = ? AND status = 'active'");
    $stmt->execute([$id]);
    $wrestler = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($wrestler) {
        // Get wrestler's match history
        $stmt = $conn->prepare("
            SELECT m.*, e.name as event_name, e.date as event_date,
                   mt.name as match_type, w.name as winner_name
            FROM matches m
            JOIN events e ON m.event_id = e.id
            JOIN match_types mt ON m.match_type_id = mt.id
            LEFT JOIN wrestlers w ON m.winner_id = w.id
            JOIN match_participants mp ON m.id = mp.match_id
            WHERE mp.wrestler_id = ?
            ORDER BY e.date DESC
        ");
        $stmt->execute([$id]);
        $match_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get wrestler's championship history
        $stmt = $conn->prepare("
            SELECT th.*, b.name as belt_name, b.image as belt_image
            FROM title_history th
            JOIN belts b ON th.belt_id = b.id
            WHERE th.wrestler_id = ?
            ORDER BY th.won_date DESC
        ");
        $stmt->execute([$id]);
        $title_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<?php include 'includes/header.php'; ?>

<?php if($wrestler): ?>
    <!-- Single Wrestler Profile -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <?php if($wrestler['image']): ?>
                    <img src="<?php echo SITE_URL; ?>/assets/uploads/wrestlers/<?php echo $wrestler['image']; ?>" 
                         class="img-fluid rounded" alt="<?php echo $wrestler['name']; ?>">
                <?php endif; ?>
                
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">Statistics</h5>
                        <ul class="list-unstyled">
                            <li><strong>Height:</strong> <?php echo $wrestler['height']; ?></li>
                            <li><strong>Weight:</strong> <?php echo $wrestler['weight']; ?></li>
                        </ul>
                        
                        <?php if($wrestler['signature_moves']): ?>
                            <h5 class="card-title mt-3">Signature Moves</h5>
                            <p class="card-text"><?php echo nl2br($wrestler['signature_moves']); ?></p>
                        <?php endif; ?>
                        
                        <div class="social-links mt-3">
                            <?php if($wrestler['twitter']): ?>
                                <a href="<?php echo $wrestler['twitter']; ?>" target="_blank" class="btn btn-outline-primary me-2">
                                    <i class="fab fa-twitter"></i> Twitter
                                </a>
                            <?php endif; ?>
                            <?php if($wrestler['instagram']): ?>
                                <a href="<?php echo $wrestler['instagram']; ?>" target="_blank" class="btn btn-outline-primary me-2">
                                    <i class="fab fa-instagram"></i> Instagram
                                </a>
                            <?php endif; ?>
                            <?php if($wrestler['facebook']): ?>
                                <a href="<?php echo $wrestler['facebook']; ?>" target="_blank" class="btn btn-outline-primary">
                                    <i class="fab fa-facebook"></i> Facebook
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <h1><?php echo $wrestler['name']; ?></h1>
                <?php if($wrestler['real_name']): ?>
                    <p class="text-muted">Real Name: <?php echo $wrestler['real_name']; ?></p>
                <?php endif; ?>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Biography</h5>
                        <p class="card-text"><?php echo nl2br($wrestler['bio']); ?></p>
                    </div>
                </div>
                
                <?php if($title_history): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Championship History</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Championship</th>
                                            <th>Won</th>
                                            <th>Lost</th>
                                            <th>Reign</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($title_history as $title): ?>
                                            <tr>
                                                <td>
                                                    <?php if($title['belt_image']): ?>
                                                        <img src="<?php echo SITE_URL; ?>/assets/uploads/belts/<?php echo $title['belt_image']; ?>" 
                                                             alt="<?php echo $title['belt_name']; ?>" height="30" class="me-2">
                                                    <?php endif; ?>
                                                    <?php echo $title['belt_name']; ?>
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
                <?php endif; ?>
                
                <?php if($match_history): ?>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Match History</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Event</th>
                                            <th>Match Type</th>
                                            <th>Result</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($match_history as $match): ?>
                                            <tr>
                                                <td><?php echo date('M d, Y', strtotime($match['event_date'])); ?></td>
                                                <td><?php echo $match['event_name']; ?></td>
                                                <td><?php echo $match['match_type']; ?></td>
                                                <td>
                                                    <?php if($match['winner_id'] == $wrestler['id']): ?>
                                                        <span class="badge bg-success">Won</span>
                                                    <?php elseif($match['winner_id']): ?>
                                                        <span class="badge bg-danger">Lost</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">No Contest</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Wrestlers Listing -->
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2>Wrestlers</h2>
            </div>
            <div class="col-md-6">
                <form action="" method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" 
                           placeholder="Search wrestlers..." value="<?php echo $search; ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
        </div>
        
        <div class="row">
            <?php foreach($wrestlers as $wrestler): ?>
                <div class="col-md-4 mb-4">
                    <div class="card wrestler-card h-100">
                        <?php if($wrestler['image']): ?>
                            <img src="<?php echo SITE_URL; ?>/assets/uploads/wrestlers/<?php echo $wrestler['image']; ?>" 
                                 class="card-img-top" alt="<?php echo $wrestler['name']; ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $wrestler['name']; ?></h5>
                            <?php if($wrestler['real_name']): ?>
                                <p class="text-muted"><?php echo $wrestler['real_name']; ?></p>
                            <?php endif; ?>
                            <p class="card-text"><?php echo substr($wrestler['bio'], 0, 100); ?>...</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-primary me-2"><?php echo $wrestler['height']; ?></span>
                                    <span class="badge bg-secondary"><?php echo $wrestler['weight']; ?></span>
                                </div>
                                <a href="?id=<?php echo $wrestler['id']; ?>" class="btn btn-primary">View Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
