<?php
// connect to the database
require_once 'db_connection.php';

// Query to fetch all books
$sql = "SELECT name, isbn, literary_genre, fiction_genre FROM books";
$result = $conn->query($sql);

// Check if any results were returned
if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        // Use htmlspecialchars to escape output and prevent XSS
        echo "name: " . htmlspecialchars($row["name"]) .
            " - isbn: " . htmlspecialchars($row["isbn"]) .
            " - literary_genre: " . htmlspecialchars($row["literary_genre"]) .
            " - fiction_genre: " . htmlspecialchars($row["fiction_genre"]) .
            "<br>";
    }
} else {
    echo "0 results";
}

// Close connection
$conn->close();
?>