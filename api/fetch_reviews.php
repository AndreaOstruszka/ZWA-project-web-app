<?php
require_once __DIR__ . '/../src/db_connection.php';

$limit = 5; // Number of reviews per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT reviews.book_id, reviews.user_id, reviews.rating, reviews.review_text, reviews.created_at, users.user_name, books.title AS book_title
        FROM reviews
        JOIN users ON reviews.user_id = users.id
        JOIN books ON reviews.book_id = books.id
        ORDER BY reviews.created_at DESC
        LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$reviews = $stmt->fetchAll();

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