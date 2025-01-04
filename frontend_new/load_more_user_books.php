<?php
session_start();
require_once 'db_connection.php';

if (empty($_SESSION["user_id"])) {
    echo "Unauthorized";
    exit();
}

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

$sql = "SELECT id, name, book_cover_small FROM books
        WHERE added_by = :user_id
        ORDER BY name ASC
        LIMIT 12
        OFFSET :offset";
$stmt = $conn->prepare($sql);
$stmt->bindValue(":user_id", $_SESSION["user_id"], PDO::PARAM_INT);
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->execute();
$user_books = $stmt->fetchAll();

foreach ($user_books as $book) {
    echo '<a href="book-detail.php?bookid=' . htmlspecialchars($book["id"]) . '" title="' . htmlspecialchars($book["name"]) . '"><div class="book-cover-image-wrapper"><img src="' . htmlspecialchars($book["book_cover_small"]) . '" alt="" height="225" width="150">' . htmlspecialchars($book["name"]) . '</div></a>';
}
?>
