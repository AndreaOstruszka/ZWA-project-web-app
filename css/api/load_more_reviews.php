<?php
session_start();

require_once __DIR__ . '/../src/db_connection.php';

if (empty($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

$sql = "SELECT reviews.id, reviews.book_id, reviews.rating, reviews.review_text, reviews.created_at, books.title AS book_title
        FROM reviews
        JOIN books ON reviews.book_id = books.id
        WHERE reviews.user_id = :user_id
        LIMIT 3 OFFSET :offset";
$stmt = $conn->prepare($sql);
$stmt->bindValue(":user_id", $_SESSION["user_id"], PDO::PARAM_INT);
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->execute();
$user_reviews = $stmt->fetchAll();

foreach ($user_reviews as $review) {
    echo "<div class='review-index'>";
    echo "<p class='review-book'><a href='../book-detail.php?bookid=" . htmlspecialchars($review["book_id"]) . "' class='link-dark'>" . htmlspecialchars($review["book_title"]) . "</a></p>";
    echo "<p class='review-rating'>" . htmlspecialchars($review["rating"]) . "/5</p>";
    echo "<div class='review-time'>" . htmlspecialchars(date('d.m.Y H:i', strtotime($review["created_at"]))) . "</div>";
    echo "<p class='review_text_index'>" . htmlspecialchars($review["review_text"]) . "</p>";
    echo "<span class='review-edit-span'><a href='../review_edit.php?review_id=" . htmlspecialchars($review["id"]) . "'><button class='button-edit'>Edit</button></a></span>";
    echo "</div>";
}
?>