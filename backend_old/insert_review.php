<?php
// Database connection
require_once 'db_connection.php';

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
        $bind = $stmt->bindValue(':rating', $rating, PDO::PARAM_STR);
        $bind = $stmt->bindValue(':review_text', $review_text, PDO::PARAM_STR);

        if ($bind === false) {
            die('Bind failed: ' . htmlspecialchars($stmt->error));
        }

        // Execute the statement
        $exec = $stmt->execute();
        if ($exec) {
            echo "Review submitted successfully.";
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

<!-- HTML form -->
<form method="POST" action="insert_review.php" id="reviewForm">
    Book ID: <input type="number" name="book_id" required><br>
    User ID: <input type="number" name="user_id" required><br>
    Rating (1-5): <input type="number" name="rating" min="1" max="5" required><br>
    Review Text: <textarea name="review_text" required></textarea><br>
    <input type="submit" value="Submit Review">
</form>

<!-- JavaScript for input validation -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('reviewForm');
        const ratingInput = form.querySelector('input[name="rating"]');
        const reviewTextInput = form.querySelector('textarea[name="review_text"]');

        function validateRating() {
            const ratingValue = parseInt(ratingInput.value, 10);
            let errorElement = ratingInput.nextElementSibling;
            if (!errorElement || !errorElement.classList.contains('error')) {
                errorElement = document.createElement('span');
                errorElement.className = 'error';
                ratingInput.parentNode.insertBefore(errorElement, ratingInput.nextSibling);
            }
            if (isNaN(ratingValue) || ratingValue < 1 || ratingValue > 5) {
                errorElement.textContent = 'Rating must be between 1 and 5.';
            } else {
                errorElement.textContent = '';
            }
        }

        function validateReviewText() {
            const reviewTextValue = reviewTextInput.value.trim();
            let errorElement = reviewTextInput.nextElementSibling;
            if (!errorElement || !errorElement.classList.contains('error')) {
                errorElement = document.createElement('span');
                errorElement.className = 'error';
                reviewTextInput.parentNode.insertBefore(errorElement, reviewTextInput.nextSibling);
            }
            if (reviewTextValue === '') {
                errorElement.textContent = 'Review text cannot be empty.';
            } else {
                errorElement.textContent = '';
            }
        }

        ratingInput.addEventListener('input', validateRating);
        reviewTextInput.addEventListener('input', validateReviewText);

        form.addEventListener('submit', function(event) {
            validateRating();
            validateReviewText();

            const errors = form.querySelectorAll('.error');
            let valid = true;
            errors.forEach(function(errorElement) {
                if (errorElement.textContent !== '') {
                    valid = false;
                }
            });

            if (!valid) {
                event.preventDefault();
            }
        });
    });
</script>

<style>
    .error { color: red; font-size: 0.9em; }
</style>