<?php
$db_servername = "localhost";
$db_username = "andy";
$db_password = "andy123";
$db_name = "ostruand";

$conn = new mysqli($db_servername, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>