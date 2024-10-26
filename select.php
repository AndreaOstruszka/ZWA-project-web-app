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

// Determine the current page number
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;

// Query to fetch books with pagination
$sql = "SELECT name, isbn, literary_genre, fiction_genre FROM books LIMIT $items_per_page OFFSET $offset";
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

// Query to get the total number of books
$total_sql = "SELECT COUNT(*) as total FROM books";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_books = $total_row['total'];
$total_pages = ceil($total_books / $items_per_page);

// Display pagination links
for ($i = 1; $i <= $total_pages; $i++) {
    echo "<a href='select.php?page=$i'>$i</a> ";
}

// Close connection
$conn->close();
?>