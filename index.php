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

// Query to fetch all messages
$sql = "SELECT name, isbn, literary_genre, fiction_genre FROM books";
$result = $conn->query($sql);

// Check if any results were returned
if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "name: " . $row["name"] . " - isbn: " . $row["isbn"] . " - literary_genre: " . $row["literary_genre"] . " - fiction_genre: " . $row["fiction_genre"] . "<br>";
    }
} else {
    echo "0 results";
}

// Close connection
$conn->close();