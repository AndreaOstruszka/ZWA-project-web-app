<?php
session_start();

/**
 * Logs out the user by clearing session and cookie data.
 *
 * This function clears the current session and destroys it to log out the user.
 * It then redirects the user to the login page. If an error occurs during the process,
 * it catches the exception and displays the error message.
 */
function logoutUser() {
    try {
        // Clear all session variables
        session_unset();
        // Destroy the session
        session_destroy();
    } catch (Throwable $th) {
        // Display the error message if an exception occurs
        echo $th;
    }
    // Redirect to the login page
    header("Location: ../index.php");
    exit();
}

// Call the logoutUser function to log out the user
logoutUser();
?>