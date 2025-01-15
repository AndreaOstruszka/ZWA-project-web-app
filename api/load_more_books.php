<?php

require_once __DIR__ . '/../src/db_connection.php';
require_once __DIR__ . '/../src/cover_check.php'; // Include the cover image check function

/**
 * Fetches top-rated books based on genre.
 *
 * This function retrieves the top-rated books for a given genre from the database.
 * It calculates the average rating for each book and orders the results by the average rating.
 *
 * @param string $genre The genre of books to filter by.
 * @param int $limit The number of books to retrieve.
 * @param int $offset The offset for pagination.
 * @return array The list of top-rated books with their average ratings.
 */
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

/**
 * Fetches the most reviewed books.
 *
 * This function retrieves the books with the highest number of reviews from the database.
 * It orders the results by the count of reviews in descending order.
 *
 * @param int $limit The number of books to retrieve.
 * @param int $offset The offset for pagination.
 * @return array The list of most reviewed books with their review counts.
 */
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

// Get the genre from the query parameters, default to an empty string if not set
$genre = isset($_GET['genre']) ? $_GET['genre'] : '';

// Get the offset for pagination, default to 0 if not set
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

// Set the limit for the number of books to retrieve
$limit = 5;

// Fetch books based on the genre or popularity
if ($genre == 'popular') {
    $books = getMostReviewedBooks($limit, $offset);
} else {
    $books = getTopRatedBooks($genre, $limit, $offset);
}

// Display each book with a link to its detail page and cover image
foreach ($books as $book) {
    echo '<a href="book_detail.php?bookid=' . htmlspecialchars($book["id"], ENT_QUOTES, 'UTF-8') . '" title="' . htmlspecialchars($book["title"], ENT_QUOTES, 'UTF-8') . '">
            <div class="book-cover-image-wrapper">
                <img src="' . getCoverImageSmall($book['title']) . '"
                     alt="' . htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') . '"
                     class="book-cover-mini">' . htmlspecialchars($book["title"], ENT_QUOTES, 'UTF-8') . '
            </div>
          </a>';
}
?>