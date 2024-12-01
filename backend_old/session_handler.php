<?php
session_start();

// Function to check if a user is logged in
function isUserLoggedIn() {
    return isset($_SESSION["user_id"]);
}

// Function to get the logged-in user's information
function getLoggedInUser() {
    if (isUserLoggedIn()) {
        return [
            "user_id" => $_SESSION["user_id"],
            "user_name" => $_SESSION["user_name"]
        ];
    }
    return null;
}
?>