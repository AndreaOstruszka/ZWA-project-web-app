<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'src/db_connection.php';

/**
 * Fetch the most reviewed books from the database.
 *
 * @param int $limit The number of books to fetch.
 * @return array The list of most reviewed books with their details.
 */
function getMostReviewedBooks($limit)
{
    global $conn;
    $stmt = $conn->prepare("SELECT books.id, books.title, books.author, COUNT(reviews.id) AS review_count, FORMAT(AVG(reviews.rating), 1) AS average_rating
                            FROM books
                            JOIN reviews ON books.id = reviews.book_id
                            GROUP BY books.id
                            ORDER BY review_count DESC
                            LIMIT :limit");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetch the top-rated books of a specific genre from the database.
 *
 * @param string $genre The genre of books to fetch.
 * @param int $limit The number of books to fetch.
 * @return array The list of top-rated books with their details.
 */
function getTopRatedBooks($genre, $limit)
{
    global $conn;
    $stmt = $conn->prepare("SELECT books.id, books.title, books.author, books.release_date, books.description_short, FORMAT(AVG(reviews.rating), 1) AS average_rating
                            FROM books
                            JOIN reviews ON books.id = reviews.book_id
                            WHERE books.fiction_genre = :genre
                            GROUP BY books.id
                            ORDER BY average_rating DESC
                            LIMIT :limit");
    $stmt->bindParam(':genre', $genre, PDO::PARAM_STR);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$limit = 5; // Number of records per page
$genres = ['romance', 'scifi', 'fantasy', 'horror', 'other']; // List of genres
$books_by_genre = []; // Array to store books by genre

// Fetch top-rated books for each genre
foreach ($genres as $genre) {
    $books_by_genre[$genre] = getTopRatedBooks($genre, $limit);
}

// Fetch the most reviewed books
$popular_books = getMostReviewedBooks($limit);

include 'header.php';

?>
    <script src="js/scroll_nav.js" defer></script>
    <div id="content">
        <nav id="genres">
            <ul class="ul-genres">
                <li class="li-genres"><a class="genres-item" href="#chart_popular">Popular</a></li>
                <li class="li-genres"><a class="genres-item" href="#chart_romance">Romance</a></li>
                <li class="li-genres"><a class="genres-item" href="#chart_scifi">Sci-Fi</a></li>
                <li class="li-genres"><a class="genres-item" href="#chart_fantasy">Fantasy</a></li>
                <li class="li-genres"><a class="genres-item" href="#chart_horror">Horror</a></li>
                <li class="li-genres"><a class="genres-item" href="#chart_other">Other</a></li>
            </ul>
        </nav>
        <article id="main-wider">
            <h1>Charts</h1>

            <?php
            // Display the most popular books
            echo '<h2>Most Popular</h2>';
            echo '<table class="book-table fixed-width">';
            echo '<tr>';
            echo '<th class="title_table">Title</th>';
            echo '<th class="author_table">Author</th>';
            echo '<th class="rating_table">Rating</th>';
            echo '</tr>';
            foreach($popular_books as $book) {
                echo "<tr>";
                echo "<td class='title_table' ><a href='book_detail.php?bookid=" . htmlspecialchars($book["id"]) . "' class='link-dark'>" . htmlspecialchars($book["title"]) . "</a></td>";
                echo '<td class="author_table">' . htmlspecialchars($book["author"]) . '</td>';
                echo '<td class="rating_table">' . htmlspecialchars($book["average_rating"]) . '</td>';
                echo "</tr>";
            }
            echo '</table> <div class="spacing"></div><br>';

            // Display the top-rated books for each genre
            foreach ($genres as $genre) {
                echo '<h2 id="chart_' . $genre . '">Popular ' . ucfirst($genre) . '</h2>';
                echo '<table class="book-table fixed-width">';
                echo '<tr>';
                echo '<th class="title_table">Title</th>';
                echo '<th class="author_table">Author</th>';
                echo '<th class="rating_table">Rating</th>';
                echo '</tr>';
                foreach ($books_by_genre[$genre] as $book) {
                    echo "<tr>";
                    echo "<td class='title_table'><a href='book_detail.php?bookid=" . htmlspecialchars($book["id"]) . "' class='link-dark'>" . htmlspecialchars($book["title"]) . "</a></td>";
                    echo '<td class="author_table">' . htmlspecialchars($book["author"]) . '</td>';
                    echo '<td class="rating_table">' . htmlspecialchars($book["average_rating"]) . '</td>';
                    echo '</tr>';
                }
                echo '</table><br>';
            }
            ?>
        </article>
    </div>
<?php include 'footer.php'; ?>