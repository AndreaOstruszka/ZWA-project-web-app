<?php
/**
 * Check if an ISBN already exists in the database.
 *
 * This script checks if a given ISBN already exists in the `books` table.
 * It uses a prepared statement to query the database and returns `true` if the ISBN exists,
 * otherwise it returns `false`.
 */

require_once __DIR__ . '/../src/db_connection.php';

// Check if the 'isbn' parameter is set and not empty in the GET request
if (isset($_GET["isbn"]) && !empty($_GET["isbn"])) {
    // Get the ISBN from the GET request
    $isbn = $_GET["isbn"];

    // SQL query to count the number of books with the given ISBN
    $sql = "SELECT COUNT(id) FROM books WHERE isbn = :isbn";

    // Prepare the SQL statement
    $stmt = $conn->prepare($sql);

    // Execute the SQL query with the ISBN parameter
    $stmt->execute(['isbn' => $isbn]);

    // Fetch the count of books with the given ISBN
    $result = $stmt->fetchColumn();

    // Output 'true' if the ISBN exists, otherwise 'false'
    if ($result > 0) {
        echo "true";
    } else {
        echo "false";
    }
} else {
    // Output an error message if the ISBN is not provided
    echo "ISBN is required";
}
?>