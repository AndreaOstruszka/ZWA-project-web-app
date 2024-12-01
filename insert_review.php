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
        $stmt = $conn->prepare("INSERT INTO reviews (book_id, user_id, rating, review_text) VALUES (?, ?, ?, ?)");
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }

        $bind = $stmt->bind_param("iiis", $book_id, $user_id, $rating, $review_text);
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

        // Close statement
        $stmt->close();
    } else {
        // Display errors
        foreach ($errors as $error) {
            echo htmlspecialchars($error) . "<br>";
        }
    }
}

// Close connection
$conn->close();
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

        form.addEventListener('submit', function(event) {
            let valid = true;

            // Validate rating
            const ratingValue = parseInt(ratingInput.value, 10);
            if (isNaN(ratingValue) || ratingValue < 1 || ratingValue > 5) {
                valid = false;
                alert("Rating must be between 1 and 5. JS");
            }

            // Validate review text
            const reviewTextValue = reviewTextInput.value.trim();
            if (reviewTextValue === "") {
                valid = false;
                alert("Review text cannot be empty. JS");
            }

            // If validation fails, prevent form submission
            if (!valid) {
                event.preventDefault();  // Prevent form submission if validation fails
            }
        });
    });
</script>