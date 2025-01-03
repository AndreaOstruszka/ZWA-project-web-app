<?php
session_start();

require_once 'db_connection.php';

$sql = "SELECT reviews.book_id, reviews.user_id, reviews.rating, reviews.review_text, reviews.created_at, users.user_name, books.name AS book_name
        FROM reviews
        JOIN users ON reviews.user_id = users.id
        JOIN books ON reviews.book_id = books.id
        ORDER BY reviews.created_at DESC";
$stmt = $conn->prepare($sql);
if($stmt->execute()) {
    $reviews = $stmt->fetchAll();
} else {
    die("Error fetching reviews.");
}

?>


<?php include 'header.php'; ?>

<div id="content">
    <article id="main-widest">
        <h1>Recently posted reviews</h1>
        <h2>What do other users think?</h2>
        <br>

        <?php
        foreach ($reviews as $review) {
            echo "<div class='review-index'>";
            echo "<div class='review-time'>" . htmlspecialchars(date('d.m.Y H:i', strtotime($review["created_at"]))) . "</div>";
            echo "<p>Book: <span class='review-book'><a href='book-detail.php?bookid=" . htmlspecialchars($review["book_id"]) . "' class='link-dark'>" . htmlspecialchars($review["book_name"]) . "</a></span></p>";
            echo "<p>Review by: <strong class='review-user'>" . htmlspecialchars($review["user_name"]) . "</strong></p>";
            echo "<p>Rating: <strong class='review-rating'>" . htmlspecialchars($review["rating"]) . "/5</strong></p>";
            echo "<p class='review-text'>" . htmlspecialchars($review["review_text"]) . "</p>";
            echo "</div>";
        }
        ?>

    </article>
</div>

<?php include 'footer.php'; ?>
