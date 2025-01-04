<?php
session_start();

if (empty($_SESSION["user_id"])) {
    $_SESSION["redirect_to"] = $_SERVER["REQUEST_URI"];
    header("Location: login.php");
    exit();
}

require_once 'db_connection.php';

$sql = "SELECT user_name, first_name, last_name, email FROM users
        WHERE id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindValue(":user_id", $_SESSION["user_id"]);
if ($stmt->execute()) {
    $logged_user = $stmt->fetch();
} else {
    die("Error fetching user data.");
}

$sql = "SELECT reviews.id, reviews.book_id, reviews.rating, reviews.review_text, reviews.created_at, books.name AS book_name
        FROM reviews
        JOIN books ON reviews.book_id = books.id
        WHERE reviews.user_id = :user_id
        LIMIT 3";
$stmt = $conn->prepare($sql);
$stmt->bindValue(":user_id", $_SESSION["user_id"]);
if ($stmt->execute()) {
    $user_reviews = $stmt->fetchAll();
} else {
    die("Error fetching reviews.");
}

?>

<?php include 'header.php'; ?>

<div id="content">
    <article id="main-widest">
        <h1>My profile</h1>
        <h2>Profile details</h2>

        <div class="profile-container">
            <div class="profile-info">
                <dl>
                    <dt>Nickname:</dt>
                    <dd><?php echo htmlspecialchars($logged_user["user_name"]); ?></dd>
                    <dt>First name:</dt>
                    <dd><?php echo htmlspecialchars($logged_user["first_name"]); ?></dd>
                    <dt>Last name:</dt>
                    <dd><?php echo htmlspecialchars($logged_user["last_name"]); ?></dd>
                    <dt>Email:</dt>
                    <dd><?php echo htmlspecialchars($logged_user["email"]); ?></dd>
                </dl>

                <div class="profile-links">
                    <a href="profile-edit.php">Edit profile</a>
                </div>
            </div>
        </div>

        <div id="review_container">
            <h2>My reviews</h2>
            <div id="review_container">

                <?php
                foreach ($user_reviews as $review) {
                    echo "<div class='review-index'>";
                    echo "<p class='review-book'><a href='book-detail.php?bookid=" . htmlspecialchars($review["book_id"]) . "' class='link-dark'>" . htmlspecialchars($review["book_name"]) . "</a></p>";
                    echo "<p class='review-rating'>" . htmlspecialchars($review["rating"]) . "/5</p>";
                    echo "<div class='review-time'>" . htmlspecialchars(date('m.d.Y H:i', strtotime($review["created_at"]))) . "</div>";
                    echo "<p class='review_text_index'>" . htmlspecialchars($review["review_text"]) . "</p>";
                    echo "<span class='review-edit-span'><a href='review_edit.php?review_id=" . htmlspecialchars($review["id"]) . "'><button class='button-edit'>Edit</button></a></span>";
                    echo "</div>";
                }
                ?>

            </div>
        </div>

        <div class="button-container">
            <button class="load-more button" data-genre="review" data-offset="3">More</button>
        </div>

        <div id="books_container">
            <?php
            if ($_SESSION["user_role"] == "admin") {

                $sql = "SELECT id, name, book_cover_small FROM books
                        WHERE added_by = :user_id
                        ORDER BY name ASC
                        LIMIT 12";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(":user_id", $_SESSION["user_id"], PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $user_books = $stmt->fetchAll();
                } else {
                    die("Error fetching books.");
                }

                echo '<div><h2>Books inserted by me</h2> <div class="book-container">';

                foreach ($user_books as $book) {
                    echo '<a href="book-detail.php?bookid=' . htmlspecialchars($book["id"]) . '" title="Book Title"><div class="book-cover-image-wrapper"><img src="' . htmlspecialchars($book["book_cover_small"]) . '" alt="" height="225" width="150">' . htmlspecialchars($book["name"]) . '</div></a>';
                }
                echo '</div><div class="spacing"></div></div>';
                echo '<div class="button-container"><button class="load-more button" data-genre="books" data-offset="12">More</button></div>';
            }
            ?>
        </div>



    </article>
</div>

<!-- JavaScript for loading more reviews -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const loadMoreButton = document.querySelector('.load-more');

        loadMoreButton.addEventListener('click', function () {
            const offset = parseInt(this.getAttribute('data-offset'));
            const container = document.getElementById('review_container');

            fetch(`load_more_reviews.php?offset=${offset}`)
                .then(response => response.text())
                .then(data => {
                    container.insertAdjacentHTML('beforeend', data);
                    this.setAttribute('data-offset', offset + 3);
                })
                .catch(error => console.error('Error fetching data:', error));
        });
    });
</script>

<!-- JavaScript for loading more books -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const loadMoreButton = document.querySelector('.load-more[data-genre="books"]');

        loadMoreButton.addEventListener('click', function () {
            const offset = parseInt(this.getAttribute('data-offset'));
            const container = document.querySelector('.book-container');

            fetch(`load_more_user_books.php?offset=${offset}`)
                .then(response => response.text())
                .then(data => {
                    container.insertAdjacentHTML('beforeend', data);
                    this.setAttribute('data-offset', offset + 12);
                })
                .catch(error => console.error('Error fetching data:', error));
        });
    });
</script>

<?php include 'footer.php'; ?>
