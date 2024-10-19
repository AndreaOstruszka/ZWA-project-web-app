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

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO books (name, isbn, author_id, literary_genre, fiction_genre) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $isbn, $author_id, $literary_genre, $fiction_genre);

// Set parameters and execute
$name = "The Lord of the Rings"; // Book title
$isbn = "978-0544003415"; // Sample ISBN for The Lord of the Rings
$author_id = "1"; // Assuming author ID is '1'
$literary_genre = "prose"; // Literary genre
$fiction_genre = "fantasy"; // Fiction genre

if ($stmt->execute()) {
    echo "New record created successfully";
} else {
    echo "Error: " . $stmt->error;
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
