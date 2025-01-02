<?php
$db_servername = "localhost";
$db_username = "andy";//"andy";
$db_password = "andy123"; //"andy123";
$db_name = "ostruand";

$conn = new PDO("mysql:host=$db_servername;dbname=$db_name", $db_username, $db_password);


/*if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}*/
?>