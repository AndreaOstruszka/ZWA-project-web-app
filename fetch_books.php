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

// Determine the current page number from AJAX request
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;

// Fetch books for the specified page
$sql = "SELECT name, isbn, literary_genre, fiction_genre FROM books LIMIT $items_per_page OFFSET $offset";
$result = $conn->query($sql);

$books = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = [
            'name' => htmlspecialchars($row["name"]),
            'isbn' => htmlspecialchars($row["isbn"]),
            'literary_genre' => htmlspecialchars($row["literary_genre"]),
            'fiction_genre' => htmlspecialchars($row["fiction_genre"])
        ];
    }
}

// Fetch total number of books for pagination info
$total_sql = "SELECT COUNT(*) as total FROM books";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_books = $total_row['total'];
$total_pages = ceil($total_books / $items_per_page);

// Prepare response data
$response = [
    'books' => $books,
    'total_pages' => $total_pages,
    'current_page' => $page
];

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>