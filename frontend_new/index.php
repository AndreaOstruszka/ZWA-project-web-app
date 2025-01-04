<?php
session_start();

require_once 'db_connection.php';

// New reviews
$sql = "SELECT reviews.book_id, reviews.user_id, reviews.rating, reviews.review_text, reviews.created_at, users.user_name, books.name AS book_name
        FROM reviews
        JOIN users ON reviews.user_id = users.id
        JOIN books ON reviews.book_id = books.id
        ORDER BY reviews.created_at DESC
        LIMIT 2";
$stmt = $conn->prepare($sql);
if($stmt->execute()) {
    $reviews = $stmt->fetchAll();
} else {
    die("Error fetching reviews.");
}

// Most popular
$sql = "SELECT books.id, books.name, books.author, books.release_date, books.description_short, FORMAT(AVG(reviews.rating), 1) AS average_rating
        FROM books
        JOIN reviews ON books.id = reviews.book_id
        GROUP BY books.id
        ORDER BY average_rating DESC
        LIMIT 10";
$stmt = $conn->prepare($sql);
if($stmt->execute()) {
    $popular_books = $stmt->fetchAll();
} else {
    die("Error fetching popular books.");
}

// New release
$sql = "SELECT id, name, author, release_date, description_short
        FROM books
        ORDER BY release_date DESC
        LIMIT 1";
$stmt = $conn->prepare($sql);
if($stmt->execute()) {
    $new_books = $stmt->fetchAll();
} else {
    die("Error fetching popular books.");
}

?>


<?php include 'header.php'; ?>

<div id="content">
    <article id="main-widest">
        <h1>Welcome to BookNook!</h1>
        <div class="main-container">
            <div class="section welcome">
                <h2>Everything about your favourite books</h2>
                <p>Hello there!
                    <br><br>
                    On this website you can explore a vast collection of books across various genres,
                    including popular titles, fantasy, sci-fi, and more. Discover detailed information, read reviews,
                    and easily keep track of newly released books all in one place.
                    <br>
                    Connect with fellow book lovers by
                    sharing your own reviews and ratings. Stay updated with the latest trends and see what’s popular
                    among other readers.
                    <br>
                    Dive into the world of literature and make BookNook your go-to destination for
                    all things books!
                </p>
            </div>


            <!-- New release section -->
            <div class="section new_release">
                <h2>New release</h2>
                <div class="new-release-container">
                    <div class="book-cover-div">
                        <!-- TODO: DYNAMIC -->
                        <img src="images/covers/cover-hobbit.jpg" alt="Hobbit" class="book-cover-mini">
                    </div>
                    <div class="book-info-mini">

                        <?php
                        if (!empty($new_books)) {
                            $book = $new_books[0];
                            echo "<p>Name: <a href='book-detail.php?bookid=" . htmlspecialchars($book['id']) . "' class='link-dark'>" . htmlspecialchars($book['name']) . "</a></p>";
                            echo "<p>Author: " . htmlspecialchars($book['author']) . "</p>";
                            echo "<p>Release date: " . htmlspecialchars(date('d.m.Y', strtotime($book['release_date']))) . "</p>";
                            echo "<p>" . htmlspecialchars($book['description_short']) . "</p>";
                        } else {
                            echo "<p>No new releases available.</p>";
                        }
                        ?>

                    </div>
                </div>
            </div>

            <!-- Most popular section -->
            <div class="section most_popular">
                <h2>Most popular</h2>
                <table>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Rating</th>
                    </tr>

                    <?php
                    foreach($popular_books as $book) {
                        echo "<tr>";
                        echo "<td><a href='book-detail.php?bookid=" . htmlspecialchars($book["id"]) . "' class='link-dark'>" . htmlspecialchars($book["name"]) . "</a></td>";
                        echo "<td>" . htmlspecialchars($book["author"]) . "</td>";
                        echo "<td>" . htmlspecialchars($book["average_rating"]) . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>

            <!-- New reviews section -->
            <div class="section new_reviews">
                <h2>New reviews</h2>

                <?php
                foreach($reviews as $review) {
                    echo "<div class='review-index'>";
                    echo "<div class='review-time'>" . htmlspecialchars(date('d.m.Y H:i', strtotime($review["created_at"]))) . "</div>";
                    echo "<p>Book: <span class='review-book'><a href='book-detail.php?bookid=" . htmlspecialchars($review["book_id"]) . "' class='link-dark'>" . htmlspecialchars($review["book_name"]) . "</a></span></p>";
                    echo "<p>Review by: <strong class='review-user'>" . htmlspecialchars($review["user_name"]) . "</strong></p>";
                    echo "<p>Rating: <strong class='review-rating'>" . htmlspecialchars($review["rating"]) . "/5</strong></p>";
                    echo "<p class='review-text'>" . htmlspecialchars($review["review_text"]) . "</p>";
                    echo "</div>";
                }
                ?>

            </div>
        </div>
    </article>
</div>

<?php include 'footer.php'; ?>
