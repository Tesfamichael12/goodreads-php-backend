<?php
session_start();
header('Content-Type: application/json');

require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "User not authenticated."
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$review_id = $_POST['review_id'] ?? null;
$action = $_POST['action'] ?? null; // 'like' or 'unlike'

if (!$review_id || !in_array($action, ['like', 'unlike'])) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid parameters."
    ]);
    exit;
}

// Connect to database
$database = new Database();
$pdo = $database->getConnection();

if ($action === 'like') {
    $stmt = $pdo->prepare("INSERT IGNORE INTO review_likes (user_id, review_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $review_id]);
} else {
    $stmt = $pdo->prepare("DELETE FROM review_likes WHERE user_id = ? AND review_id = ?");
    $stmt->execute([$user_id, $review_id]);
}
// Get updated like count
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM review_likes WHERE review_id = ?");
$countStmt->execute([$review_id]);
$like_count = $countStmt->fetchColumn();
echo json_encode([
    "success" => true,
    "like_count" => (int)$like_count,
    "message" => $action === 'like' ? "Review liked successfully." : "Review unliked successfully."
]);
exit;
?>