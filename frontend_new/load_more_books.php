<?php

require_once 'db_connection.php';
require_once 'cover_check.php'; // Include the cover image check function

function getTopRatedBooks($genre, $limit, $offset) {
    global $conn;
    $stmt = $conn->prepare("SELECT books.id, books.title, CAST(AVG(reviews.rating) AS CHAR(3)) average_rating
                            FROM books
                            LEFT JOIN reviews ON books.id = reviews.book_id
                            WHERE books.fiction_genre = :genre
                            GROUP BY books.id
                            ORDER BY average_rating DESC
                            LIMIT :limit OFFSET :offset");
    $stmt->bindParam(':genre', $genre, PDO::PARAM_STR);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMostReviewedBooks($limit, $offset) {
    global $conn;
    $stmt = $conn->prepare("SELECT books.id, books.title, COUNT(reviews.id) AS review_count
                            FROM books
                            LEFT JOIN reviews ON books.id = reviews.book_id
                            GROUP BY books.id
                            ORDER BY review_count DESC
                            LIMIT :limit OFFSET :offset");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$genre = isset($_GET['genre']) ? $_GET['genre'] : '';
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = 5;

if ($genre == 'popular') {
    $books = getMostReviewedBooks($limit, $offset);
} else {
    $books = getTopRatedBooks($genre, $limit, $offset);
}

foreach ($books as $book) {
    echo '<a href="book-detail.php?bookid=' . htmlspecialchars($book["id"], ENT_QUOTES, 'UTF-8') . '" title="' . htmlspecialchars($book["title"], ENT_QUOTES, 'UTF-8') . '">
            <div class="book-cover-image-wrapper">
                <img src="' . getCoverImageSmall($book['title']) . '"
                     alt="' . htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') . '"
                     class="book-cover-mini">' . htmlspecialchars($book["title"], ENT_QUOTES, 'UTF-8') . '
            </div>
          </a>';
}
?>