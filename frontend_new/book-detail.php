<?php
session_start();

// Database connection
require_once 'db_connection.php';

if(isset($_GET["bookid"])){
    $book_id = $_GET["bookid"];
} else {
    die("Book not specified");
}

$sql = "SELECT name, isbn, literary_genre, fiction_genre, author, book_cover_large, description_long, CAST((sum(reviews.rating)/count(reviews.rating)) AS VARCHAR(3)) AS rating
        FROM books LEFT JOIN reviews ON reviews.book_id = books.id
        WHERE books.id = :book_id";
$stmt = $conn->prepare($sql);
$stmt->bindValue(":book_id", $book_id);
if($stmt->execute()) {
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
if($stmt->execute()) {
    $user_reviews = $stmt->fetchAll();
} else {
    die("Error fetching reviews.");
}

// Initialize error message variables
$errors = [];

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
    if (empty($errors)) {
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
            header("Location: book-detail.php?bookid=$book_id");
        } else {
            echo "Error: Could not save the review to the database.";
        }

    } else {
        // Display errors
        foreach ($errors as $error) {
            echo htmlspecialchars($error) . "<br>";
        }
    }
}
?>

<?php include 'header.php'; ?>

    <div id="content">
        <article id="main-widest">
            <h1>Book detail</h1>
            <h2><?php echo htmlspecialchars($current_book['name']); ?></h2>
            <div class="book-container">
                <div class="book-cover-div">
                    <img src="images/covers/cover-hobbit.jpg" alt="Hobbit" class="book-cover-mini">
                </div>
                <div class="book-info">
                    <div class="rating">
                        <span>Rating: <?php echo $current_book["rating"]?>/5</span>
                    </div>

                    <dl>
                        <dt>Author:</dt>
                        <dd><?php echo htmlspecialchars($current_book['author'])?></dd>
                        <dt>ISBN:</dt>
                        <dd><?php echo htmlspecialchars($current_book['isbn'])?></dd>
                        <dt>Literary genre:</dt>
                        <dd><?php echo htmlspecialchars($current_book['literary_genre'])?></dd>
                        <dt>Fictional genre:</dt>
                        <dd><?php echo htmlspecialchars($current_book['fiction_genre'])?></dd>
                        <dt>Description:</dt>
                        <dd><?php echo htmlspecialchars($current_book['description_long'])?></dd>
                        <br>
                    </dl>
                </div>
            </div>
            <br>

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
                if(isset($_SESSION["user_id"])) {
                    include 'review-create.php';
                }
                ?>
            </div>
        </article>
    </div>
<?php include 'footer.php'; ?>