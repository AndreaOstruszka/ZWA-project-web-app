<?php
/**
 * Check if a username already exists in the database.
 *
 * This script checks if a given username already exists in the `users` table.
 * It uses a prepared statement to query the database and returns `true` if the username exists,
 * otherwise it returns `false`.
 */

require_once '../src/db_connection.php';

// Check if the 'username' parameter is set in the GET request
if (isset($_GET['username'])) {
    // Trim any whitespace from the username
    $username = trim($_GET['username']);

    // SQL query to count the number of users with the given username
    $sql = "SELECT COUNT(*) FROM users WHERE user_name = :username";

    // Prepare the SQL statement
    $stmt = $conn->prepare($sql);

    // Bind the username parameter to the SQL query
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);

    // Execute the SQL query
    $stmt->execute();

    // Fetch the count of users with the given username
    $count = $stmt->fetchColumn();

    // Output 'true' if the username exists, otherwise 'false'
    if ($count > 0) {
        echo "true";
    } else {
        echo "false";
    }
}
?>