<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'src/db_connection.php'; // Include the database connection
require_once 'src/cover_check.php'; // Include the cover image check function

/**
 * Fetch the latest 2 reviews from the database.
 *
 * @var string $sql SQL query to fetch the latest 2 reviews.
 * @var PDOStatement $stmt Prepared statement for executing the SQL query.
 * @var array $reviews Array to store the fetched reviews.
 */
$sql = "SELECT reviews.book_id, reviews.user_id, reviews.rating, reviews.review_text, reviews.created_at, users.user_name, books.title AS book_title
        FROM reviews
        JOIN users ON reviews.user_id = users.id
        JOIN books ON reviews.book_id = books.id
        ORDER BY reviews.created_at DESC
        LIMIT 2";
$stmt = $conn->prepare($sql);
if ($stmt->execute()) {
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC); // Store the fetched reviews
} else {
    die("Error fetching reviews."); // Handle error
}

/**
 * Fetch the top 10 most popular books based on average rating.
 *
 * @var string $sql SQL query to fetch the top 10 most popular books.
 * @var PDOStatement $stmt Prepared statement for executing the SQL query.
 * @var array $popular_books Array to store the fetched popular books.
 */
$sql = "SELECT books.id, books.title, books.author, books.release_date, books.description_short, FORMAT(AVG(reviews.rating), 1) AS average_rating
        FROM books
        JOIN reviews ON books.id = reviews.book_id
        GROUP BY books.id
        ORDER BY average_rating DESC
        LIMIT 10";
$stmt = $conn->prepare($sql);
if ($stmt->execute()) {
    $popular_books = $stmt->fetchAll(PDO::FETCH_ASSOC); // Store the fetched popular books
} else {
    die("Error fetching popular books."); // Handle error
}

/**
 * Fetch the latest book release from the database.
 *
 * @var string $sql SQL query to fetch the latest book release.
 * @var PDOStatement $stmt Prepared statement for executing the SQL query.
 * @var array $new_books Array to store the fetched new release.
 */
$sql = "SELECT id, title, author, release_date, description_short
        FROM books
        ORDER BY release_date DESC
        LIMIT 1";
$stmt = $conn->prepare($sql);
if ($stmt->execute()) {
    $new_books = $stmt->fetchAll(PDO::FETCH_ASSOC); // Store the fetched new release
} else {
    die("Error fetching new releases."); // Handle error
}
?>

<?php require_once 'header.php'; // Include the header ?>

    <div id="content">
        <article id="main-widest">
            <h1>Welcome to BookNook!</h1>
            <div class="main-container">
                <div class="section welcome">
                    <h2>Everything about your favourite books</h2>
                    <p>Hello there!
                        <br><br>
                        On this website you can explore a vast collection of books across various genres,
                        including popular titles, fantasy, sci-fi, and more. Discover detailed information, read
                        reviews,
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
                        <?php if (!empty($new_books)) {
                            $book = $new_books[0]; ?>
                            <div class="book-cover-div">
                                <img src="<?php echo getCoverImageSmall($book['title']); ?>"
                                     alt="<?php echo htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8'); ?>"
                                     class="book-cover-mini">
                            </div>
                        <?php } ?>
                        <div class="book-info-mini">
                            <?php
                            if (!empty($new_books)) {
                                $book = $new_books[0];
                                echo "<p>Title: <a href='book_detail.php?bookid=" . htmlspecialchars($book['id'], ENT_QUOTES, 'UTF-8') . "' class='link-dark'>" . htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') . "</a></p>";
                                echo "<p>Author: " . htmlspecialchars($book['author'], ENT_QUOTES, 'UTF-8') . "</p>";
                                echo "<p>Release date: " . htmlspecialchars(date('m.d.Y', strtotime($book['release_date'])), ENT_QUOTES, 'UTF-8') . "</p>";
                                echo "<p>" . htmlspecialchars($book['description_short'], ENT_QUOTES, 'UTF-8') . "</p>";
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
                        foreach ($popular_books as $book) {
                            echo "<tr>";
                            echo "<td><a href='book_detail.php?bookid=" . htmlspecialchars($book["id"], ENT_QUOTES, 'UTF-8') . "' class='link-dark'>" . htmlspecialchars($book["title"], ENT_QUOTES, 'UTF-8') . "</a></td>";
                            echo "<td>" . htmlspecialchars($book["author"], ENT_QUOTES, 'UTF-8') . "</td>";
                            echo "<td>" . htmlspecialchars($book["average_rating"], ENT_QUOTES, 'UTF-8') . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
                <!-- New reviews section -->
                <div class="section new_reviews">
                    <h2>New reviews</h2>
                    <?php
                    foreach ($reviews as $review) {
                        echo "<div class='review-index'>";
                        echo "<div class='review-time'>" . htmlspecialchars(date('m.d.Y H:i', strtotime($review["created_at"])), ENT_QUOTES, 'UTF-8') . "</div>";
                        echo "<p>Book: <span class='review-book'><a href='book_detail.php?bookid=" . htmlspecialchars($review["book_id"], ENT_QUOTES, 'UTF-8') . "' class='link-dark'>" . htmlspecialchars($review["book_title"], ENT_QUOTES, 'UTF-8') . "</a></span></p>";
                        echo "<p>Review by: <strong class='review-user'>" . htmlspecialchars($review["user_name"], ENT_QUOTES, 'UTF-8') . "</strong></p>";
                        echo "<p>Rating: <strong class='review-rating'>" . htmlspecialchars($review["rating"], ENT_QUOTES, 'UTF-8') . "/5</strong></p>";
                        echo "<p class='review-text'>" . htmlspecialchars($review["review_text"], ENT_QUOTES, 'UTF-8') . "</p>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
        </article>
    </div>
<?php include 'footer.php'; // Include the footer ?>