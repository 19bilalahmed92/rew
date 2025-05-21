<?php
// Database connection settings
$host = 'localhost';
$dbname = 'rew_wrestling';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password
        $email = $_POST['email'];
        $full_name = $_POST['full_name'];

        $stmt = $pdo->prepare("INSERT INTO admins (username, password, email, full_name) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $password, $email, $full_name]);

        echo "<p>✅ Admin created successfully!</p>";
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Admin</title>
</head>
<body>
    <h2>Create New Admin</h2>
    <form method="POST" action="">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Full Name:</label><br>
        <input type="text" name="full_name" required><br><br>

        <button type="submit">Create Admin</button>
    </form>
</body>
</html>
