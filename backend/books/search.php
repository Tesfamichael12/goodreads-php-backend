<?php
include_once '../includes/db.php';
header('Content-Type: application/json');

$database = new Database();
$pdo = $database->getConnection();

$query = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($query === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Search query is required']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT * FROM books WHERE title LIKE :title OR author LIKE :author');
    $like = "%$query%";
    $stmt->bindParam(':title', $like, PDO::PARAM_STR);
    $stmt->bindParam(':author', $like, PDO::PARAM_STR);
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($books);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
