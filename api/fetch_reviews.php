<?php
/**
 * Fetch and display reviews.
 *
 * This script fetches reviews from the database and displays them.
 * It includes information about the book, user, rating, and review text.
 * The reviews are paginated using the page and limit parameters.
 */

require_once __DIR__ . '/../src/db_connection.php';

// Set the number of reviews per page
$limit = 5;

// Get the current page number from the query parameters, default to 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the offset for pagination
$offset = ($page - 1) * $limit;

// SQL query to fetch reviews with user and book details
$sql = "SELECT reviews.book_id, reviews.user_id, reviews.rating, reviews.review_text, reviews.created_at, users.user_name, books.title AS book_title
        FROM reviews
        JOIN users ON reviews.user_id = users.id
        JOIN books ON reviews.book_id = books.id
        ORDER BY reviews.created_at DESC
        LIMIT :limit OFFSET :offset";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);

// Bind the limit and offset parameters to the SQL query
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

// Execute the SQL query
$stmt->execute();

// Fetch all reviews
$reviews = $stmt->fetchAll();

// Display each review with details
foreach ($reviews as $review) {
    echo "<div class='review-index'>";
    echo "<div class='review-time'>" . htmlspecialchars(date('m.d.Y H:i', strtotime($review["created_at"]))) . "</div>";
    echo "<p>Book: <span class='review-book'><a href='book_detail.php?bookid=" . htmlspecialchars($review["book_id"]) . "' class='link-dark'>" . htmlspecialchars($review["book_title"]) . "</a></span></p>";
    echo "<p>Review by: <strong class='review-user'>" . htmlspecialchars($review["user_name"]) . "</strong></p>";
    echo "<p>Rating: <strong class='review-rating'>" . htmlspecialchars($review["rating"]) . "/5</strong></p>";
    echo "<p class='review-text'>" . htmlspecialchars($review["review_text"]) . "</p>";
    echo "</div>";
}
?>