<?php
// Database configuration
$dsn = 'sqlite:database/users.db';

// Create database connection
try {
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Create users table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    security_key TEXT NOT NULL
)";
$pdo->exec($sql);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $securityKey = $_POST['security_key'] ?? '';

    // Validate inputs
    if (empty($email) || empty($password) || empty($securityKey)) {
        echo "All fields are required.";
        exit;
    }

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert the user into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO users (email, password, security_key) VALUES (?, ?, ?)");
        $stmt->execute([$email, $hashedPassword, $securityKey]);
        echo "Registration successful.";
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') { // Duplicate entry error code
            echo "Email is already registered.";
        } else {
            echo "An error occurred: " . $e->getMessage();
        }
    }
}
?>
