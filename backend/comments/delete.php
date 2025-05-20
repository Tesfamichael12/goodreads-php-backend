<!-- delete comment logic goes here -->
 <?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['comment_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing comment_id']);
    exit;
}

$user_id = $_SESSION['user_id'];
$comment_id = intval($data['comment_id']);

// Get review_id for updating comment_count
$stmt = $pdo->prepare("SELECT review_id FROM comments WHERE id = ? AND user_id = ?");
$stmt->execute([$comment_id, $user_id]);
$row = $stmt->fetch();
if (!$row) {
    echo json_encode(['error' => 'Not found or not allowed']);
    exit;
}
$review_id = $row['review_id'];

// Delete comment
$stmt = $pdo->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
$stmt->execute([$comment_id, $user_id]);

// Update comment_count
$stmt = $pdo->prepare("UPDATE reviews SET comment_count = comment_count - 1 WHERE id = ?");
$stmt->execute([$review_id]);

echo json_encode(['success' => true]);