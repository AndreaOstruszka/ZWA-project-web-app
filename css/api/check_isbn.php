<?php
require_once __DIR__ . '/../src/db_connection.php';


if(isset($_GET["isbn"]) && !empty($_GET["isbn"])) {
    $isbn = $_GET["isbn"];
    $sql = "SELECT COUNT(id) FROM books WHERE isbn = :isbn";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['isbn' => $isbn]);
    $result = $stmt->fetchColumn();
    if ($result > 0) {
        echo "true";
    } else {
        echo "false";
    }
} else {
    echo "ISBN is required";
}