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

// Function to delete a book by id
function deleteBookById($conn, $id) {
    // Prepare the SQL statement
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");

    // Check if the statement was prepared successfully
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("i", $id);

    // Execute the statement
    if ($stmt->execute() === false) {
        die("Execute failed: " . $stmt->error);
    } else {
        echo "Book with ID $id has been deleted successfully.";
    }

    // Close the statement
    $stmt->close();
}

// Specify the ID of the book to delete
$idToDelete = 4; // Change this to the desired ID

// Call the function to delete the book
deleteBookById($conn, $idToDelete);

// Close the connection
$conn->close();
?>