<?php
require_once 'includes/config.php';

// Create required directories if they don't exist
$directories = [
    UPLOAD_PATH,
    WRESTLER_IMAGES,
    EVENT_IMAGES,
    BELT_IMAGES,
    SLIDER_IMAGES
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
        echo "Created directory: $dir<br>";
    }
}

// Create a default wrestler image if it doesn't exist
$default_image_path = $_SERVER['DOCUMENT_ROOT'] . '/rew_pakistan/assets/images/default-wrestler.jpg';
if (!file_exists($default_image_path)) {
    // Create a simple default image using GD
    $image = imagecreatetruecolor(400, 400);
    $bg_color = imagecolorallocate($image, 240, 240, 240);
    $text_color = imagecolorallocate($image, 100, 100, 100);
    
    imagefill($image, 0, 0, $bg_color);
    imagestring($image, 5, 150, 190, 'No Image', $text_color);
    
    imagejpeg($image, $default_image_path, 90);
    imagedestroy($image);
    
    echo "Created default wrestler image<br>";
}

echo "Setup completed successfully!";
?> 