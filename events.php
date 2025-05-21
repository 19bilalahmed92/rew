<?php
require_once 'includes/config.php';

// Get search parameters
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$status = isset($_GET['status']) ? sanitizeInput($_GET['status']) : 'upcoming';

// Build query
$query = "SELECT * FROM events WHERE 1=1";
$params = [];

if($search) {
    $query .= " AND (name LIKE ? OR location LIKE ? OR description LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
}

if($status) {
    $query .= " AND status = ?";
    $params[] = $status;
}

$query .= " ORDER BY date " . ($status == 'upcoming' ? 'ASC' : 'DESC');

// Get events
$conn = getDBConnection();
$stmt = $conn->prepare($query);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get single event if ID is provided
$event = null;
if(isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($event) {
        // Get event matches
        $stmt = $conn->prepare("
            SELECT m.*, mt.name as match_type, w.name as winner_name,
                   GROUP_CONCAT(DISTINCT wp.name ORDER BY mp.team_number, wp.name SEPARATOR ' vs ') as participants
            FROM matches m
            JOIN match_types mt ON m.match_type_id = mt.id
            LEFT JOIN wrestlers w ON m.winner_id = w.id
            JOIN match_participants mp ON m.id = mp.match_id
            JOIN wrestlers wp ON mp.wrestler_id = wp.id
            WHERE m.event_id = ?
            GROUP BY m.id
            ORDER BY m.match_order
        ");
        $stmt->execute([$id]);
        $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<?php include 'includes/header.php'; ?>

<?php if($event): ?>
    <!-- Single Event Details -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <h1><?php echo $event['name']; ?></h1>
                <div class="event-date mb-3">
                    <?php echo date('F d, Y', strtotime($event['date'])); ?>
                </div>
                <div class="text-muted mb-4">
                    <i class="fas fa-map-marker-alt"></i> <?php echo $event['location']; ?>
                </div>
                
                <?php if($event['poster_image']): ?>
                    <img src="<?php echo SITE_URL; ?>/assets/uploads/events/<?php echo $event['poster_image']; ?>" 
                         class="img-fluid rounded mb-4" alt="<?php echo $event['name']; ?>">
                <?php endif; ?>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Event Description</h5>
                        <p class="card-text"><?php echo nl2br($event['description']); ?></p>
                    </div>
                </div>
                
                <?php if($matches): ?>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Match Card</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Match</th>
                                            <th>Type</th>
                                            <th>Winner</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($matches as $match): ?>
                                            <tr>
                                                <td><?php echo $match['participants']; ?></td>
                                                <td><?php echo $match['match_type']; ?></td>
                                                <td>
                                                    <?php if($match['winner_name']): ?>
                                                        <span class="badge bg-success"><?php echo $match['winner_name']; ?></span>
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
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Event Information</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <strong>Date:</strong><br>
                                <?php echo date('F d, Y', strtotime($event['date'])); ?>
                            </li>
                            <li class="mb-2">
                                <strong>Time:</strong><br>
                                <?php echo date('h:i A', strtotime($event['date'])); ?>
                            </li>
                            <li class="mb-2">
                                <strong>Location:</strong><br>
                                <?php echo $event['location']; ?>
                            </li>
                            <li class="mb-2">
                                <strong>Status:</strong><br>
                                <span class="badge bg-<?php echo $event['status'] == 'upcoming' ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst($event['status']); ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Events Listing -->
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2>Events</h2>
            </div>
            <div class="col-md-6">
                <form action="" method="GET" class="d-flex">
                    <select name="status" class="form-select me-2">
                        <option value="upcoming" <?php echo $status == 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                        <option value="past" <?php echo $status == 'past' ? 'selected' : ''; ?>>Past</option>
                    </select>
                    <input type="text" name="search" class="form-control me-2" 
                           placeholder="Search events..." value="<?php echo $search; ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
        </div>
        
        <div class="row">
            <?php foreach($events as $event): ?>
                <div class="col-md-4 mb-4">
                    <div class="card event-card h-100">
                        <?php if($event['poster_image']): ?>
                            <img src="<?php echo SITE_URL; ?>/assets/uploads/events/<?php echo $event['poster_image']; ?>" 
                                 class="card-img-top" alt="<?php echo $event['name']; ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="event-date mb-3">
                                <?php echo date('F d, Y', strtotime($event['date'])); ?>
                            </div>
                            <h5 class="card-title"><?php echo $event['name']; ?></h5>
                            <p class="card-text"><?php echo substr($event['description'], 0, 100); ?>...</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo $event['location']; ?>
                                </span>
                                <a href="?id=<?php echo $event['id']; ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
