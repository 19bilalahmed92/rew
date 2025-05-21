<?php
require_once 'includes/config.php';

// Get active sliders
$conn = getDBConnection();
$stmt = $conn->query("SELECT * FROM sliders WHERE status = 'active' ORDER BY order_number ASC");
$sliders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get upcoming events
$stmt = $conn->query("SELECT * FROM events WHERE status = 'upcoming' ORDER BY date ASC LIMIT 5");
$upcoming_events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get featured wrestlers
$stmt = $conn->query("SELECT * FROM wrestlers WHERE status = 'active' ORDER BY RAND() LIMIT 6");
$featured_wrestlers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get current champions
$stmt = $conn->query("
    SELECT b.*, w.name as champion_name, w.image as champion_image 
    FROM belts b 
    LEFT JOIN title_history th ON b.id = th.belt_id 
    LEFT JOIN wrestlers w ON th.wrestler_id = w.id 
    WHERE b.status = 'active' 
    AND (th.lost_date IS NULL OR th.lost_date > CURRENT_TIMESTAMP)
    ORDER BY th.won_date DESC
");
$current_champions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<!-- Main Slider -->
<div id="mainSlider" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <?php foreach($sliders as $key => $slider): ?>
            <button type="button" data-bs-target="#mainSlider" data-bs-slide-to="<?php echo $key; ?>" 
                    <?php echo $key === 0 ? 'class="active"' : ''; ?>></button>
        <?php endforeach; ?>
    </div>
    
    <div class="carousel-inner">
        <?php foreach($sliders as $key => $slider): ?>
            <div class="carousel-item <?php echo $key === 0 ? 'active' : ''; ?>" 
                 style="background-image: url('<?php echo SITE_URL; ?>/assets/uploads/sliders/<?php echo $slider['image']; ?>')">
                <div class="carousel-caption">
                    <h2><?php echo $slider['title']; ?></h2>
                    <p><?php echo $slider['description']; ?></p>
                    <?php if($slider['link']): ?>
                        <a href="<?php echo $slider['link']; ?>" class="btn btn-primary">Learn More</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <button class="carousel-control-prev" type="button" data-bs-target="#mainSlider" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#mainSlider" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<!-- Upcoming Events Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">Upcoming Events</h2>
        <div class="row">
            <?php foreach($upcoming_events as $event): ?>
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
                                <a href="events.php?id=<?php echo $event['id']; ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="events.php" class="btn btn-outline-primary">View All Events</a>
        </div>
    </div>
</section>

<!-- Featured Wrestlers Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4">Featured Wrestlers</h2>
        <div class="row">
            <?php foreach($featured_wrestlers as $wrestler): ?>
                <div class="col-md-4 mb-4">
                    <div class="card wrestler-card h-100">
                        <?php if($wrestler['image'] && file_exists(UPLOAD_PATH . 'wrestlers/' . $wrestler['image'])): ?>
                            <img src="<?php echo SITE_URL; ?>/assets/uploads/wrestlers/<?php echo $wrestler['image']; ?>" 
                                 class="card-img-top" alt="<?php echo $wrestler['name']; ?>">
                        <?php else: ?>
                            <img src="<?php echo SITE_URL; ?>/assets/images/default-wrestler.jpg" 
                                 class="card-img-top" alt="<?php echo $wrestler['name']; ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $wrestler['name']; ?></h5>
                            <p class="card-text"><?php echo substr($wrestler['bio'], 0, 100); ?>...</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-primary me-2"><?php echo $wrestler['height']; ?></span>
                                    <span class="badge bg-secondary"><?php echo $wrestler['weight']; ?></span>
                                </div>
                                <a href="wrestlers.php?id=<?php echo $wrestler['id']; ?>" class="btn btn-primary">View Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="wrestlers.php" class="btn btn-outline-primary">View All Wrestlers</a>
        </div>
    </div>
</section>

<!-- Current Champions Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">Current Champions</h2>
        <div class="row">
            <?php foreach($current_champions as $champion): ?>
                <div class="col-md-4 mb-4">
                    <div class="card belt-card h-100">
                        <?php if($champion['image']): ?>
                            <img src="<?php echo SITE_URL; ?>/assets/uploads/belts/<?php echo $champion['image']; ?>" 
                                 class="card-img-top" alt="<?php echo $champion['name']; ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $champion['name']; ?></h5>
                            <?php if($champion['champion_name']): ?>
                                <p class="card-text">
                                    <strong>Current Champion:</strong><br>
                                    <?php echo $champion['champion_name']; ?>
                                </p>
                            <?php else: ?>
                                <p class="card-text text-muted">Vacant</p>
                            <?php endif; ?>
                            <a href="belts.php?id=<?php echo $champion['id']; ?>" class="btn btn-primary">View History</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="belts.php" class="btn btn-outline-primary">View All Championships</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
