<?php
require_once '../src/db_connection.php';

if (isset($_GET['username'])) {
    $username = trim($_GET['username']);
    $sql = "SELECT COUNT(*) FROM users WHERE user_name = :username";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo "true";
    } else {
        echo "false";
    }
}
?>