<?php
// Database connection
require_once 'db_connection.php';

// Fetch authors, literary genres, and fiction genres from the database
$authors = $conn->query("SELECT id, name, surname FROM authors");
$literary_genres = $conn->query("SELECT DISTINCT literary_genre FROM books");
$fiction_genres = $conn->query("SELECT DISTINCT fiction_genre FROM books");

// Function to validate and sanitize input
function validate_input($data) {
    $data = trim($data); // Remove whitespace from the beginning and end
    $data = stripslashes($data); // Remove backslashes
    $data = htmlspecialchars($data); // Convert special characters to HTML entities
    return $data;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize form fields
    $name = validate_input($_POST["name"]);
    $isbn = validate_input($_POST["isbn"]);
    $author_id = validate_input($_POST["author_id"]);
    $literary_genre = validate_input($_POST["literary_genre"]);
    $fiction_genre = validate_input($_POST["fiction_genre"]);

    // Validate that fields are not empty and check ISBN format
    if (empty($name) || empty($isbn) || empty($author_id) || empty($literary_genre) || empty($fiction_genre)) {
        echo "All fields are required.";
    } elseif (!ctype_digit($isbn) || (strlen($isbn) !== 10 && strlen($isbn) !== 13)) {
        echo "ISBN must be a 10 or 13 digit number.";
    } else {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO books (name, isbn, author_id, literary_genre, fiction_genre) VALUES (?, ?, ?, ?, ?)");      // Prepare statement
        $stmt->bind_param("sssss", $name, $isbn, $author_id, $literary_genre, $fiction_genre);

        // Execute the statement
        if ($stmt->execute()) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    }
}

// Close connection
$conn->close();
?>

<!-- HTML form to collect book data -->
<form method="POST" action="">
    Name: <input type="text" name="name" required><br>
    ISBN: <input type="text" name="isbn" required><br>
    Author:
    <select name="author_id" required>
        <?php while ($author = $authors->fetch_assoc()): ?>
            <option value="<?php echo htmlspecialchars($author['id']); ?>">
                <?php echo htmlspecialchars($author['name'] . ' ' . $author['surname']); ?>
            </option>
        <?php endwhile; ?>
    </select><br>
    Literary Genre:
    <select name="literary_genre" required>
        <?php while ($genre = $literary_genres->fetch_assoc()): ?>
            <option value="<?php echo htmlspecialchars($genre['literary_genre']); ?>"><?php echo htmlspecialchars($genre['literary_genre']); ?></option>
        <?php endwhile; ?>
    </select><br>
    Fiction Genre:
    <select name="fiction_genre" required>
        <?php while ($genre = $fiction_genres->fetch_assoc()): ?>
            <option value="<?php echo htmlspecialchars($genre['fiction_genre']); ?>"><?php echo htmlspecialchars($genre['fiction_genre']); ?></option>
        <?php endwhile; ?>
    </select><br>
    <input type="submit" value="Submit">
</form>

<!-- JavaScript for ISBN validation -->
<script>
    function validateISBN() {
        const isbn = document.querySelector('input[name="isbn"]').value;
        const errorMessage = document.getElementById('isbn-error');

        if (!/^\d{10}$|^\d{13}$/.test(isbn)) {
            errorMessage.textContent = "ISBN must be a 10 or 13 digit number.";
            return false;
        } else {
            errorMessage.textContent = "";
            return true;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('bookForm');
        form.addEventListener('submit', function(event) {
            if (!validateISBN()) {
                event.preventDefault(); // Prevent form submission if ISBN is invalid
            }
        });

        document.querySelector('input[name="isbn"]').addEventListener('input', validateISBN);
    });
</script>