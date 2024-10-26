<?php
// Database credentials
$servername = "localhost";
$username = "andy";
$password = "andy123";
$dbname = "ostruand";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to validate and sanitize input
function validate_input($data) {
    return trim($data);
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
        $stmt = $conn->prepare("INSERT INTO books (name, isbn, author_id, literary_genre, fiction_genre) VALUES (?, ?, ?, ?, ?)");
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
    Author ID: <input type="text" name="author_id" required><br>
    Literary Genre: <input type="text" name="literary_genre" required><br>
    Fiction Genre: <input type="text" name="fiction_genre" required><br>
    <input type="submit" value="Submit">
</form>