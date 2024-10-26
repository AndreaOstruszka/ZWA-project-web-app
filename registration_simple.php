<?php
// Database connection
$servername = "localhost";
$username = "andy";
$password = "andy123";
$dbname = "ostruand";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error message variables
$errors = [];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Load and sanitize input data
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $user_name = trim($_POST["user_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $password_confirm = $_POST["password_confirm"];

    // Form data validation
    if (empty($first_name)) {
        $errors["first_name"] = "First name is required.";
    }
    if (empty($last_name)) {
        $errors["last_name"] = "Last name is required.";
    }
    if (empty($user_name)) {
        $errors["user_name"] = "User name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "A valid email is required.";
    }
    if (empty($password)) {
        $errors["password"] = "Password is required.";
    } elseif ($password !== $password_confirm) {
        $errors["password_confirm"] = "Passwords do not match.";
    }

    // Check if user_name or email already exists in the database
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE user_name = ? OR email = ?");
        $stmt->bind_param("ss", $user_name, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "This user_name or email is already registered.";
        }
        $stmt->close();
    }

    // If no errors, insert user into the database
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $role = 'registered_user'; // Default role

        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, user_name, email, password_hash, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $first_name, $last_name, $user_name, $email, $password_hash, $role);

        if ($stmt->execute()) {
            echo "Registration successful!";
        } else {
            echo "An error occurred during registration: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // Display errors
        foreach ($errors as $error) {
            echo htmlspecialchars($error) . "<br>";
        }
    }
}

$conn->close();
?>

<!-- HTML form -->
<form method="POST" action="">
    First Name: <input type="text" name="first_name" required><br>
    Last Name: <input type="text" name="last_name" required><br>
    User Name: <input type="text" name="user_name" required><br>
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    Confirm Password: <input type="password" name="password_confirm" required><br>
    <input type="submit" value="Register">
</form>