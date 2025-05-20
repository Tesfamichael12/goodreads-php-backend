<!-- comment adding logic goes here -->
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
if (!isset($data['review_id'], $data['content'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing data']);
    exit;
}

$user_id = $_SESSION['user_id'];
$review_id = intval($data['review_id']);
$content = trim($data['content']);

$stmt = $pdo->prepare("INSERT INTO comments (review_id, user_id, content) VALUES (?, ?, ?)");
$stmt->execute([$review_id, $user_id, $content]);

// Optionally update comment_count in reviews table
$stmt = $pdo->prepare("UPDATE reviews SET comment_count = comment_count + 1 WHERE id = ?");
$stmt->execute([$review_id]);

echo json_encode(['success' => true]);