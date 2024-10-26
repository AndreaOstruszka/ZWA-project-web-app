<?php
// Start session at the top
session_start();

// Generate a new CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the CSRF token is valid
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token!");
    }

    // Clear the CSRF token to prevent reuse
    unset($_SESSION['csrf_token']);

    // Database connection parameters
    $servername = "localhost";
    $username = "andy";
    $password = "andy123";
    $dbname = "ostruand";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Form data processing with validation...
    $errors = [];
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $user_name = trim($_POST["user_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $password_confirm = $_POST["password_confirm"];

    // Data validation...
    if (empty($first_name)) { $errors["first_name"] = "First name is required."; }
    if (empty($last_name)) { $errors["last_name"] = "Last name is required."; }
    if (empty($user_name)) { $errors["user_name"] = "User name is required."; }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors["email"] = "A valid email is required."; }
    if (empty($password)) { $errors["password"] = "Password is required."; }
    elseif ($password !== $password_confirm) { $errors["password_confirm"] = "Passwords do not match."; }

    if (empty($errors)) {
        // Check if user already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE user_name = ? OR email = ?");
        $stmt->bind_param("ss", $user_name, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "This user_name or email is already registered.";
        }
        $stmt->close();
    }

    // If no errors, insert data and redirect
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $role = 'registered_user';
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, user_name, email, password_hash, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $first_name, $last_name, $user_name, $email, $password_hash, $role);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: registration_success.php");
            exit();
        } else {
            echo "Registration error: " . $stmt->error;
        }
    } else {
        foreach ($errors as $error) {
            echo htmlspecialchars($error) . "<br>";
        }
    }
}

// Form rendering with CSRF token as hidden field
?>

<!-- JavaScript for validating password confirmation -->
<script>
    function validatePassword() {
        const password = document.querySelector('input[name="password"]').value;
        const passwordConfirm = document.querySelector('input[name="password_confirm"]').value;
        const errorMessage = document.getElementById('password-error');

        if (password !== passwordConfirm) {
            errorMessage.textContent = "Passwords do not match.";
        } else {
            errorMessage.textContent = ""; // Clear error message if they match
        }
    }

    // Add event listeners to the password fields
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('input[name="password"]').addEventListener('input', validatePassword);
        document.querySelector('input[name="password_confirm"]').addEventListener('input', validatePassword);
    });
</script>

<form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    First Name: <input type="text" name="first_name" value="<?php echo htmlspecialchars($first_name ?? ''); ?>" required><br>
    Last Name: <input type="text" name="last_name" value="<?php echo htmlspecialchars($last_name ?? ''); ?>" required><br>
    User Name: <input type="text" name="user_name" value="<?php echo htmlspecialchars($user_name ?? ''); ?>" required><br>
    Email: <input type="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required><br>
    Password: <input type="password" name="password" required><br>
    Confirm Password: <input type="password" name="password_confirm" required><br>
    <div id="password-error" style="color: red;"></div> <!-- Error message display -->
    <input type="submit" value="Register">
</form>