<?php
$db_servername = "localhost";
$db_username = "ostruand";
$db_password = "webove aplikace";
$db_name = "ostruand";

$conn = new PDO("mysql:host=$db_servername;dbname=$db_name", $db_username, $db_password);


/*if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}*/
?>