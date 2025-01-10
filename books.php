<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'src/db_connection.php';
require_once 'src/cover_check.php';

// Books by genre
function getTopRatedBooks($genre, $limit, $offset)
{
    global $conn;
    $stmt = $conn->prepare("SELECT books.id, books.title, CAST(AVG(reviews.rating) AS CHAR(3)) AS average_rating
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


// Popular books
function getMostReviewedBooks($limit, $offset)
{
    global $conn;
    $stmt = $conn->prepare("SELECT books.id, books.title, books.author, COUNT(reviews.id) AS review_count
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

$limit = 5; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$genres = ['romance', 'scifi', 'fantasy', 'horror', 'other'];
$books_by_genre = [];

foreach ($genres as $genre) {
    $books_by_genre[$genre] = getTopRatedBooks($genre, $limit, $offset);
}

$popular_books = getMostReviewedBooks($limit, $offset);

include 'header.php';

?>
    <script src="js/scroll_nav.js" defer></script>
    <script src="js/load_more_books.js" defer></script>
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
        <div id="main-wider">
            <h1>Books</h1>

            <?php
            echo '<div><h2>Popular</h2> <div class="book-container" id="popular_books">';

            foreach ($popular_books as $book) {
                echo '<a href="book_detail.php?bookid=' . htmlspecialchars($book["id"], ENT_QUOTES, 'UTF-8') . '" title="Book Title">
            <div class="book-cover-image-wrapper">
                <img src="' . getCoverImageSmall($book['title']) . '"
                     alt="' . htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') . '"
                     class="book-cover-mini">' . htmlspecialchars($book["title"], ENT_QUOTES, 'UTF-8') . '
            </div>
            </a>';
            }
            echo '</div><div class="spacing"></div></div>';

            ?>

            <div class="button-container">
                <button class="load-more button" data-genre="popular" data-offset="5">More</button>
            </div>

            <div class="spacing"></div>

            <?php
            foreach ($genres as $genre) {
                echo '<h2 id="chart_' . $genre . '">' . ucfirst($genre) . '</h2>';
                echo '<div class="book-container" id="' . $genre . '_books">';
                foreach ($books_by_genre[$genre] as $book) {
                    echo '<a href="book_detail.php?bookid=' . htmlspecialchars($book["id"]) . '" title="Book Title"><div class="book-cover-image-wrapper"><img src="' . getCoverImageSmall($book['title']) . '" alt="' . htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') . '" class="book-cover-mini">' . htmlspecialchars($book["title"]) . '</div></a>';
                }
                echo '</div>';
                echo '<div class="button-container">';
                echo '<button class="load-more button" data-genre="' . $genre . '" data-offset="5">More</button>';
                echo '</div>';
                echo '<div class="spacing"></div>';
            }
            ?>
        </div>
    </div>
<?php include 'footer.php'; ?>