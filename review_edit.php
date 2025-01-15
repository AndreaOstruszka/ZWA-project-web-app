<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'src/db_connection.php';

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$review_text = $review_rating = "";
$errors = [
    'review_text' => '',
    'rating' => ''
];

if (isset($_GET["review_id"])) {
    $review_id = $_GET["review_id"];
} else {
    die("Review not specified.");
}

// Fetch the reviewer's user ID
$stmt = $conn->prepare("SELECT user_id FROM reviews WHERE id = :review_id");
$stmt->bindValue(':review_id', $review_id, PDO::PARAM_INT);
$stmt->execute();
$review = $stmt->fetch();
if (!$review) {
    die("Review not found.");
}

$reviewer_id = $review['user_id'];

// Check if the current user is the one who wrote the review or an admin
if ($user_id !== $reviewer_id && $_SESSION["user_role"] !== "admin") {
    header("Location: reviews.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["edit"])) {
        $review_text = trim($_POST["review_text"]);

        if (empty($review_text)) {
            $errors["review_text"] = "Review text is required.";
        }

        if (strpos($review_text, "(edited)") !== 0) {
            $review_text = "(edited) " . $review_text;
        }
        $review_rating = filter_input(INPUT_POST, 'review_rating', FILTER_VALIDATE_INT);

        if ($review_rating === false || $review_rating < 1 || $review_rating > 5) {
            $errors["rating"] = "Rating must be between 1 and 5.";
        }

        if (empty(array_filter($errors))) {
            $stmt = $conn->prepare("UPDATE reviews SET review_text = :review_text, rating = :rating, created_at = NOW()
                                    WHERE id = :review_id");
            $stmt->bindValue(':review_text', $review_text, PDO::PARAM_STR);
            $stmt->bindValue(':rating', $review_rating, PDO::PARAM_INT);
            $stmt->bindValue(':review_id', $review_id, PDO::PARAM_INT);
            //$stmt->bindValue(':user_id', $_SESSION["user_id"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                header("Location: profile.php");
                exit();
            } else {
                echo "Error updating review.";
            }
        }
    } elseif (isset($_POST["delete"])) {
        $stmt = $conn->prepare("DELETE FROM reviews WHERE id = :review_id");
        $stmt->bindValue(':review_id', $review_id, PDO::PARAM_INT);
        //$stmt->bindValue(':user_id', $_SESSION["user_id"], PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: profile.php");
            exit();
        } else {
            echo "Error deleting review.";
        }
    }
}
$stmt = $conn->prepare("SELECT review_text, rating FROM reviews WHERE id = :review_id");
$stmt->bindValue(':review_id', $review_id, PDO::PARAM_INT);
try {
    $stmt->execute();
    $review = $stmt->fetch();
    if (!$review) {
        die("Review not found.");
    }
} catch (PDOException $e) {
    die("Error fetching review." . $e->getMessage());
}

?>

<?php include 'header.php'; ?>

    <div id="content">
        <article id="main-widest">
            <h1>Edit your review</h1>
            <br>
            <div class="form-wrapper">
                <form action="review_edit.php?review_id=<?php echo htmlspecialchars($review_id); ?>" method="post"
                      enctype="multipart/form-data" class="my_form">
                    <fieldset>
                        <legend>How did you like this book?</legend>
                        <br>
                        <label for="review_text">Review:</label>
                        <textarea
                                class="form-input form-detail <?php echo !empty($errors['review_text']) ? 'error-border' : ''; ?>"
                                id="review_text" name="review_text"
                                placeholder="Is there anything you would like to say?"><?php echo htmlspecialchars(isset($_POST['review_text']) ? $_POST['review_text'] : $review["review_text"]); ?></textarea>
                        <span class="error"><?php echo $errors['review_text']; ?></span>

                        <label for="review_rating">Rating:</label>
                        <select class="form-input form-detail <?php echo !empty($errors['rating']) ? 'error-border' : ''; ?>"
                                id="review_rating" name="review_rating">
                            <option value="1" <?php if ($review["rating"] == 1) echo 'selected'; ?>>1/5</option>
                            <option value="2" <?php if ($review["rating"] == 2) echo 'selected'; ?>>2/5</option>
                            <option value="3" <?php if ($review["rating"] == 3) echo 'selected'; ?>>3/5</option>
                            <option value="4" <?php if ($review["rating"] == 4) echo 'selected'; ?>>4/5</option>
                            <option value="5" <?php if ($review["rating"] == 5) echo 'selected'; ?>>5/5</option>
                        </select>
                        <span class="error"><?php echo $errors['rating']; ?></span>
                        <br><br>

                        <div class="button-container">
                            <button class="button" name="edit">Edit</button>
                            <button class="button" name="delete">Delete</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </article>
    </div>

<?php include 'footer.php'; ?>