<!-- register logic goes here -->
<?php
// Start session and include DB connection
session_start();

$host = 'localhost';
$db   = 'goodreads_clone';
$user = 'root'; // adjust if needed
$pass = '';     // adjust if needed
$charset = 'utf8mb4';

// Set up PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get and sanitize form data
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $bio      = trim($_POST['bio'] ?? '');
    $profile_pic = 'default.png'; // Use default for now

    // Basic validation
    if (empty($name) || empty($email) || empty($password)) {
        die("All required fields must be filled.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        die("Email already registered.");
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert user into database
    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password, bio, profile_pic, role_id)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $role_id = 2; // Regular user
    $stmt->execute([$name, $email, $hashedPassword, $bio, $profile_pic, $role_id]);

    echo "Registration successful! 🎉";
    // You can redirect or log them in here if you want.
} else {
    echo "Invalid request method.";
}
?>
