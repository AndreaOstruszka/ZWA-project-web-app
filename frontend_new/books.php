<?php

include 'header.php';
require_once 'db_connection.php';

function getTopRatedBooks($genre, $limit, $offset)
{
    global $conn;
    $stmt = $conn->prepare("SELECT books.id, books.name, FORMAT(AVG(reviews.rating), 1) AS average_rating
                            FROM books
                            JOIN reviews ON books.id = reviews.book_id
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

function getMostReviewedBooks($limit, $offset)
{
    global $conn;
    $stmt = $conn->prepare("SELECT books.id, books.name, books.author, COUNT(reviews.id) AS review_count
                            FROM books
                            JOIN reviews ON books.id = reviews.book_id
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

?>
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
                    echo '<a href="book-detail.php?bookid=' . htmlspecialchars($book["id"]) . '" title="Book Title"><div class="book-cover-image-wrapper"><img src="images/covers/cover-hobbit.jpg" alt="Hobbit" class="book-cover-mini">' . htmlspecialchars($book["name"]) . '</div></a>';
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
                        echo '<a href="book-detail.php?bookid=' . htmlspecialchars($book["id"]) . '" title="Book Title"><div class="book-cover-image-wrapper"><img src="images/covers/cover-hobbit.jpg" alt="Hobbit" class="book-cover-mini">' . htmlspecialchars($book["name"]) . '</div></a>';
                    }
                    echo '</div>';
                    echo '<div class="button-container">';
                    echo '<button class="load-more button" data-genre="' . $genre . '" data-offset="5">More</button>';
                    echo '</div>';
                    echo '<div class="spacing"></div>';
                }
                ?>
        </article>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loadMoreButtons = document.querySelectorAll('.load-more');

            loadMoreButtons.forEach(button => {
                button.addEventListener('click', function () {
                    console.log("Clicked "+button.getAttribute("data-genre"));
                    const genre = this.getAttribute('data-genre');
                    const offset = parseInt(this.getAttribute('data-offset'));
                    const container = document.getElementById(genre + '_books');

                    fetch(`load_more_books.php?genre=${genre}&offset=${offset}`)
                        .then(response => response.text())
                        .then(data => {
                            container.insertAdjacentHTML('beforeend', data);
                            this.setAttribute('data-offset', offset + 5);
                        })
                        .catch(error => console.error('Error fetching data:', error));
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const links = document.querySelectorAll('a[href^="#"]');

            for (const link of links) {
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);

                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop,
                            behavior: 'smooth'
                        });
                    }
                });
            }
        });
    </script>

<?php include 'footer.php'; ?>