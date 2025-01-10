<?php
session_start();
require_once 'db_connection.php';
require_once 'cover_check.php';

if (empty($_SESSION["user_id"])) {
    echo "Unauthorized";
    exit();
}

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

$sql = "SELECT id, title, book_cover_small FROM books
        WHERE added_by = :user_id
        ORDER BY title ASC
        LIMIT 12
        OFFSET :offset";
$stmt = $conn->prepare($sql);
$stmt->bindValue(":user_id", $_SESSION["user_id"], PDO::PARAM_INT);
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->execute();
$user_books = $stmt->fetchAll();

foreach ($user_books as $book) {
    echo '<a href="book-detail.php?bookid=' . htmlspecialchars($book["id"]) . '" title="Book Title"><div class="book-cover-image-wrapper"><img src="' . getCoverImageSmall($book['title']) . '"alt="' . htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') . '"class="book-cover-mini">
                            ' . htmlspecialchars($book["title"]) . '</div></a>';
}
?>
