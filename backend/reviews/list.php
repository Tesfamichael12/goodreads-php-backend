<!-- list review logic goes here -->
 <?php
require_once '../includes/db.php';
header('Content-Type: application/json');

$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;
if (!$book_id) {
    echo json_encode(['error' => 'Missing book_id']);
    exit;
}

$stmt = $pdo->prepare(
    "SELECT r.id, r.user_id, u.name, u.profile_pic, r.rating, r.comment, r.like_count, r.comment_count, r.created_at
     FROM reviews r
     JOIN users u ON r.user_id = u.id
     WHERE r.book_id = ?
     ORDER BY r.created_at DESC"
);
$stmt->execute([$book_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'reviews' => $reviews,
    'count' => count($reviews)
]);