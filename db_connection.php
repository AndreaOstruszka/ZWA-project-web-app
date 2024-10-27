<?php
$servername = "localhost";
$username = "andy";
$password = "andy123";
$dbname = "ostruand";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>