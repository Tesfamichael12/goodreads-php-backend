<!-- list of comment logic goes here -->
 <?php
require_once '../includes/db.php';
header('Content-Type: application/json');

$review_id = isset($_GET['review_id']) ? intval($_GET['review_id']) : 0;
if (!$review_id) {
    echo json_encode(['error' => 'Missing review_id']);
    exit;
}

$stmt = $pdo->prepare(
    "SELECT c.id, c.user_id, u.name, u.profile_pic, c.content, c.like_count, c.created_at
     FROM comments c
     JOIN users u ON c.user_id = u.id
     WHERE c.review_id = ?
     ORDER BY c.created_at ASC"
);
$stmt->execute([$review_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'comments' => $comments,
    'count' => count($comments)
]);