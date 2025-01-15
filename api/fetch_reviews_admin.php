<?php
/**
 * Fetch and display reviews for admin.
 *
 * This script fetches reviews from the database and displays them for the admin.
 * It includes information about the book, user, rating, and review text.
 * The reviews are paginated using the offset and limit parameters.
 */

require_once __DIR__ . '/../src/db_connection.php';

// Get the offset for pagination, default to 0 if not set
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

// Set the limit for the number of reviews to retrieve
$limit = 3;

// SQL query to fetch reviews with user and book details
$sql = "SELECT reviews.id, reviews.book_id, reviews.user_id, reviews.rating, reviews.review_text, reviews.created_at, users.user_name, books.title AS book_title
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
    echo "<p>Book: <span class='review-book'><a href='../book_detail.php?bookid=" . htmlspecialchars($review["book_id"]) . "' class='link-dark'>" . htmlspecialchars($review["book_title"]) . "</a></span></p>";
    echo "<p>Review by: <strong class='review-user'>" . htmlspecialchars($review["user_name"]) . "</strong></p>";
    echo "<p>Rating: <strong class='review-rating'>" . htmlspecialchars($review["rating"]) . "/5</strong></p>";
    echo "<p class='review-text'>" . htmlspecialchars($review["review_text"]) . "</p>";
    echo "<span class='review-edit-span'><a href='../review_edit.php?review_id=" . htmlspecialchars($review["id"]) . "'><button class='button-edit'>Edit</button></a></span>";
    echo "</div>";
}
?>