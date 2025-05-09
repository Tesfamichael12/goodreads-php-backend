<!-- listing books logic goes here -->
<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once "../includes/db.php";

try {
    $query = "
        SELECT 
            b.id, 
            b.title, 
            b.author, 
            b.genre, 
            b.description, 
            b.published_year, 
            b.created_at, 
            b.updated_at, 
            u.username AS created_by_username
        FROM books b
        LEFT JOIN users u ON b.created_by = u.id
        ORDER BY b.created_at DESC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($books);

} catch (PDOException $e) {
    echo json_encode(["error" => "Query failed: " . $e->getMessage()]);
}
?>
