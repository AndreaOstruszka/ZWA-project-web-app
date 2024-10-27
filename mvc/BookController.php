<?php
require_once 'BookModel.php';

// Database connection
$servername = "localhost";
$username = "andy";
$password = "andy123";
$dbname = "ostruand";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$bookModel = new BookModel($conn);

// Handle AJAX request for books
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;

// Fetch books and pagination data
$books = $bookModel->getBooks($items_per_page, $offset);
$total_books = $bookModel->getTotalBooks();
$total_pages = ceil($total_books / $items_per_page);

// Send JSON response
header('Content-Type: application/json');
echo json_encode([
    'books' => $books,
    'total_pages' => $total_pages,
    'current_page' => $page
]);

$conn->close();
?>