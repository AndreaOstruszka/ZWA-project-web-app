<?php
/**
 * Load more user books script.
 *
 * This script fetches and displays more books added by the logged-in user.
 * It uses session data to identify the user and fetches books from the database
 * with pagination support.
 */

session_start();
require_once __DIR__ . '/../src/db_connection.php';
require_once __DIR__ . '/../src/cover_check.php';

// Check if the user is logged in
if (empty($_SESSION["user_id"])) {
    echo "Unauthorized";
    exit();
}

// Get the offset for pagination, default to 0 if not set
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

// SQL query to fetch books added by the user
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

// Display each book with a link to its detail page and cover image
foreach ($user_books as $book) {
    echo '<a href="book_detail.php?bookid=' . htmlspecialchars($book["id"]) . '" title="Book Title"><div class="book-cover-image-wrapper"><img src="' . getCoverImageSmall($book['title']) . '" alt="' . htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') . '" class="book-cover-mini">' . htmlspecialchars($book["title"]) . '</div></a>';
}
?>