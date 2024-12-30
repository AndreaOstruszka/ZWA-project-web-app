<?php
session_start();

// Database connection
$db_servername = "localhost";
$db_username = "andy";
$db_password = "andy123";
$db_name = "ostruand";

$conn = new mysqli($db_servername, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error array and form field values
$errors = [];
$user_name = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input data
    $user_name = htmlspecialchars(trim($_POST["user_name"]));
    $password = $_POST["password"];

    // Input validation
    if (empty($user_name)) {
        $errors["user_name"] = "Username or email is required.";
    }
    if (empty($password)) {
        $errors["password"] = "Password is required.";
    }

    // Check credentials if no validation errors
    if (empty($errors)) {
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE user_name = ? OR email = ?");
        $stmt->bind_param("ss", $user_name, $user_name);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Fetch password hash and verify
            $stmt->bind_result($user_id, $password_hash);
            $stmt->fetch();

            if (password_verify($password, $password_hash)) {
                // Successful login, create session
                $_SESSION["user_id"] = $user_id;
                $_SESSION["user_name"] = $user_name;
                echo "Login successful! Welcome, " . htmlspecialchars($user_name) . ".";
            } else {
                $errors["password"] = "Incorrect password.";
            }
        } else {
            $errors["user_name"] = "No user found with that username or email.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <style>
        .notification_error {
            color: red;
            font-size: 0.9em;
        }

    /*    .error-border {*/
    /*        border-color: red;*/
    /*    }*/
    /*</style>*/
    <script>
        function validateLoginForm() {
            console.log("Validating form...");
            let isValid = true;
            let errors = {};

            // Form fields
            const user_name = document.getElementById("user_name");
            const password = document.getElementById("password");

            // Clear previous errors
            document.querySelectorAll(".notification_error").forEach(el => el.innerText = "");    // Delete all HTML elements with class error
            document.querySelectorAll("input").forEach(el => el.classList.remove("error-border"));

            // Validate required fields
            if (user_name.value.trim() === "") {
                errors.user_name = "Username or email is required.";
                user_name.classList.add("error-border");
                isValid = false;
            }

            // If password is empty after deleting redundant characters, add error message
            if (password.value.trim() === "") {
                errors.password = "Password is required.";
                password.classList.add("error-border");
                isValid = false;
            }

            // Display errors
            for (const key in errors) {
                document.getElementById(key + "Error").innerText = errors[key];
            }

            return isValid;
        }
    </script>
</head>
<body>

<!-- HTML login form -->
<form method="POST" action="login.php" onsubmit="return validateLoginForm()">
    Username or Email: <input type="text" name="user_name" id="user_name" value="<?php echo htmlspecialchars($user_name); ?>" ><br>
    <span id="user_nameError" class="notification_error"><?php echo isset($errors["user_name"]) ? $errors["user_name"] : ''; ?></span><br>

    Password: <input type="password" name="password" id="password" ><br>
    <span id="passwordError" class="notification_error"><?php echo isset($errors["password"]) ? $errors["password"] : ''; ?></span><br>

    <input type="submit" value="Login">
</form>

</body>
</html>