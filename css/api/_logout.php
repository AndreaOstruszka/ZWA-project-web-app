<?php
session_start();

function logoutUser() {
    // Clear session and cookie data, then redirect to login page or show an error
    try {
        session_unset();
        session_destroy();
    } catch (Throwable $th) {
        echo $th;
    }
    //setcookie('user_token', '', time() - 3600);  // Delete the cookie
    header("Location: ../index.php");
    exit();
}

logoutUser();