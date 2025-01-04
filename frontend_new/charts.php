<?php

include 'header.php';
require_once 'db_connection.php';

function getMostReviewedBooks($limit)
{
    global $conn;
    $stmt = $conn->prepare("SELECT books.id, books.name, books.author, COUNT(reviews.id) AS review_count, FORMAT(AVG(reviews.rating), 1) AS average_rating
                            FROM books
                            JOIN reviews ON books.id = reviews.book_id
                            GROUP BY books.id
                            ORDER BY review_count DESC
                            LIMIT :limit");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTopRatedBooks($genre, $limit)
{
    global $conn;
    $stmt = $conn->prepare("SELECT books.id, books.name, books.author, books.release_date, books.description_short, FORMAT(AVG(reviews.rating), 1) AS average_rating
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
$genres = ['romance', 'scifi', 'fantasy', 'horror', 'other'];
$books_by_genre = [];

foreach ($genres as $genre) {
    $books_by_genre[$genre] = getTopRatedBooks($genre, $limit);
}

$popular_books = getMostReviewedBooks($limit);

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
    <article id="main-wider">
        <h1>Charts</h1>

        <?php
        echo '<h2>Most Popular</h2>';
        echo '<table class="book-table fixed-width">';
        echo '<tr>';
        echo '<th class="title_table">Title</th>';
        echo '<th class="author_table">Author</th>';
        echo '<th class="rating_table">Rating</th>';
        echo '</tr>';
        foreach($popular_books as $book) {
            echo "<tr>";
            echo "<td class='title_table' ><a href='book-detail.php?bookid=" . htmlspecialchars($book["id"]) . "' class='link-dark'>" . htmlspecialchars($book["name"]) . "</a></td>";
            echo '<td class="author_table">' . htmlspecialchars($book["author"]) . '</td>';
            echo '<td class="rating_table">' . htmlspecialchars($book["average_rating"]) . '</td>';
            echo "</tr>";
        }
        echo '</table> <div class="spacing"></div><br>';


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
                echo "<td class='title_table'><a href='book-detail.php?bookid=" . htmlspecialchars($book["id"]) . "' class='link-dark'>" . htmlspecialchars($book["name"]) . "</a></td>";
                echo '<td class="author_table">' . htmlspecialchars($book["author"]) . '</td>';
                echo '<td class="rating_table">' . htmlspecialchars($book["average_rating"]) . '</td>';
                echo '</tr>';
            }
            echo '</table><br>';
        }

        ?>






    </article>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const links = document.querySelectorAll('a[href^="#"]');

        for (const link of links) {
            link.addEventListener('click', function(event) {
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
