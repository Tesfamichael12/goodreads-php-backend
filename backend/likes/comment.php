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
$comment_id = $_POST['comment_id'] ?? null;
$action = $_POST['action'] ?? null; // 'like' or 'unlike'

if (!$comment_id || !in_array($action, ['like', 'unlike'])) {
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
    $stmt = $pdo->prepare("INSERT IGNORE INTO comment_likes (user_id, comment_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $comment_id]);
    echo json_encode([
        "success" => true,
        "message" => "Comment liked successfully."
    ]);
} else {
    $stmt = $pdo->prepare("DELETE FROM comment_likes WHERE user_id = ? AND comment_id = ?");
    $stmt->execute([$user_id, $comment_id]);
    echo json_encode([
        "success" => true,
        "message" => "Comment unliked successfully."
    ]);
}
?>