<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;
if (!$book_id) {
    echo json_encode(['error' => 'Missing book_id']);
    exit;
}

$stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as count FROM reviews WHERE book_id = ?");
$stmt->execute([$book_id]);
$row = $stmt->fetch();

echo json_encode([
    'avg_rating' => round($row['avg_rating'], 2),
    'count' => intval($row['count'])
]);