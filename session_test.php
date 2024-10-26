<?php
include 'session_handler.php';

if (isUserLoggedIn()) {
    $user = getLoggedInUser();
    echo "Welcome, " . htmlspecialchars($user["user_name"]) . "!";
} else {
    echo "You are not logged in.";
}

// Your existing code
?>