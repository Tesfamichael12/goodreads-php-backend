<!-- add a new review logic goes here -->
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
if (!isset($data['book_id'], $data['rating'], $data['comment'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing data']);
    exit;
}

$user_id = $_SESSION['user_id'];
$book_id = intval($data['book_id']);
$rating = intval($data['rating']);
$comment = trim($data['comment']);

if ($rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid rating']);
    exit;
}

// Insert or update review
$stmt = $pdo->prepare("INSERT INTO reviews (user_id, book_id, rating, comment) VALUES (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment), updated_at = NOW()");
$stmt->execute([$user_id, $book_id, $rating, $comment]);

echo json_encode(['success' => true]);