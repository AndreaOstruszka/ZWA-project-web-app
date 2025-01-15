<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
require_once 'src/db_connection.php';
require_once 'src/cover_check.php';

/**
 * Fetch book details and user reviews from the database.
 *
 * @var int $book_id The ID of the book to fetch details for.
 * @var array $current_book The details of the current book.
 * @var array $user_reviews The list of user reviews for the current book.
 * @var array $errors The list of error messages for form validation.
 */
if (isset($_GET["bookid"])) {
    $book_id = $_GET["bookid"];
} else {
    die("Book not specified");
}

$sql = "SELECT title, isbn, literary_genre, fiction_genre, author, book_cover_large, description_long, CAST((sum(reviews.rating)/count(reviews.rating)) AS CHAR(3)) AS rating
        FROM books LEFT JOIN reviews ON reviews.book_id = books.id
        WHERE books.id = :book_id";
$stmt = $conn->prepare($sql);
$stmt->bindValue(":book_id", $book_id);
if ($stmt->execute()) {
    $current_book = $stmt->fetch();
} else {
    die("Error fetching book.");
}

$sql = "SELECT reviews.book_id, reviews.user_id, reviews.rating, reviews.review_text, reviews.created_at, users.user_name
        FROM reviews
        JOIN users ON reviews.user_id = users.id
        WHERE reviews.book_id = :book_id";
$stmt = $conn->prepare($sql);
$stmt->bindValue(":book_id", $book_id);
if ($stmt->execute()) {
    $user_reviews = $stmt->fetchAll();
} else {
    die("Error fetching reviews.");
}

// Initialize error message variables
$errors = [
    'review_text' => '',
    'rating' => ''
];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $book_id = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
    $review_text = trim($_POST["review_text"]);

    // Input validation
    if ($rating === false || $rating < 1 || $rating > 5) {
        $errors["rating"] = "Rating must be between 1 and 5.";
    }
    if (empty($review_text)) {
        $errors["review_text"] = "Review text is required.";
    }

    // If no errors, proceed with saving to the database
    if (empty(array_filter($errors))) {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO reviews (book_id, user_id, rating, review_text) VALUES (:book_id, :user_id, :rating, :review_text)");
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }

        $bind = $stmt->bindValue(':book_id', $book_id, PDO::PARAM_INT);
        $bind = $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $bind = $stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
        $bind = $stmt->bindValue(':review_text', $review_text, PDO::PARAM_STR);

        if ($bind === false) {
            die('Bind failed: ' . htmlspecialchars($stmt->error));
        }

        // Execute the statement
        $exec = $stmt->execute();
        if ($exec) {
            header("Location: book_detail.php?bookid=$book_id");
        } else {
            echo "Error: Could not save the review to the database.";
        }

    }
}
?>

<?php include 'header.php'; ?>

    <div id="content">
        <article id="main-widest">
            <h1>Book detail</h1>
            <h2><?php echo htmlspecialchars($current_book['title']); ?></h2>
            <div class="book-container">
                <div class="book-cover-div">
                    <img src="<?php echo getCoverImageBig($current_book['title']) ?: 'uploads/cover_placeholder.jpg'; ?>"
                         alt="<?php echo htmlspecialchars($current_book['title'], ENT_QUOTES, 'UTF-8'); ?>"
                         class="book-cover">
                </div>

                <div class="book-info">
                    <div class="rating">
                        <span>Rating: <?php echo $current_book["rating"] ?>/5</span>
                    </div>

                    <dl>
                        <dt>Author:</dt>
                        <dd><?php echo htmlspecialchars($current_book['author']) ?></dd>
                        <dt>ISBN:</dt>
                        <dd><?php echo htmlspecialchars($current_book['isbn']) ?></dd>
                        <dt>Literary genre:</dt>
                        <dd><?php echo htmlspecialchars($current_book['literary_genre']) ?></dd>
                        <dt>Fictional genre:</dt>
                        <dd><?php echo htmlspecialchars($current_book['fiction_genre']) ?></dd>
                        <dt>Description:</dt>
                        <dd><?php echo htmlspecialchars($current_book['description_long']) ?></dd>
                    </dl>
                </div>
            </div>
            <br>

            <?php
            if (isset($_SESSION["user_role"]) && $_SESSION["user_role"] == "admin") {
                echo '<div class="profile-links">';
                echo '<a href="book_edit.php?book_id=' . htmlspecialchars($book_id) . '">Edit book</a>';
                echo '</div><br>';
            }
            ?>

            <h2>Reviews</h2>
            <div class="review-container">

                <?php
                foreach ($user_reviews as $review) {
                    echo "<div class='review-index'>";
                    echo "<span class='review-user'>" . htmlspecialchars($review["user_name"]) . "</span>";
                    echo "<span class='review-rating'>" . htmlspecialchars($review["rating"]) . "/5</span>";
                    echo "<span class='review-time'>" . date('d.m.Y H:i', strtotime($review["created_at"])) . "</span>";
                    echo "<p class='review_text_index'>" . htmlspecialchars($review["review_text"]) . "</p>";
                    echo "</div>";
                }
                ?>

                <br><br>

                <?php
                if (isset($_SESSION["user_id"])) { ?>
                    <div class="review_form framed-form">
                        <form action="#" method="post" enctype="multipart/form-data" class="my_form">
                            <fieldset>
                                <legend>How did you like this book?</legend>
                                <input type="text" id="review_user" name="user_id"
                                       value="<?php echo $_SESSION['user_id']; ?>" hidden>
                                <input type="text" id="book_id" name="book_id"
                                       value="<?php echo htmlspecialchars($book_id); ?>" hidden>

                                <label for="review-text">* Review:</label>
                                <textarea
                                        class="form-input form-detail <?php echo !empty($errors['review_text']) ? 'error-border' : ''; ?>"
                                        id="review-text" name="review_text"
                                        placeholder="Is there anything you would like to say?"
                                        required><?php echo htmlspecialchars(isset($_POST['review_text']) ? $_POST['review_text'] : ''); ?></textarea>
                                <span class="error"><?php echo $errors['review_text']; ?></span>

                                <label for="review-rating">* Rating:</label>
                                <select class="form-input form-detail <?php echo !empty($errors['rating']) ? 'error-border' : ''; ?>"
                                        id="review-rating" name="rating" required>
                                    <option value="" disabled selected>-- Select a rating --</option>
                                    <option value="1">1/5</option>
                                    <option value="2">2/5</option>
                                    <option value="3">3/5</option>
                                    <option value="4">4/5</option>
                                    <option value="5">5/5</option>
                                </select>
                                <span class="error"><?php echo $errors['rating']; ?></span>

                                <br>
                                <p>* mandatory field</p>
                                <br><br>

                                <div class="button-container">
                                    <button class="button" type="submit">Add your review</button>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                <?php } ?>
            </div>
        </article>
    </div>
<?php include 'footer.php'; ?>