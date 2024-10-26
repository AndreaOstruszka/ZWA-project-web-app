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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO books (name, isbn, author_id, literary_genre, fiction_genre) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $isbn, $author_id, $literary_genre, $fiction_genre);

    // Set parameters from form fields
    $name = $conn->real_escape_string($_POST["name"]);
    $isbn = $conn->real_escape_string($_POST["isbn"]);
    $author_id = $conn->real_escape_string($_POST["author_id"]);
    $literary_genre = $conn->real_escape_string($_POST["literary_genre"]);
    $fiction_genre = $conn->real_escape_string($_POST["fiction_genre"]);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
}

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