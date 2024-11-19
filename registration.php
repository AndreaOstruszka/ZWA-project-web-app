<?php
session_start();

// Generate CSRF token if it doesn't already exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize errors and pre-filled form data
$errors = [];
$form_data = [
    "first_name" => "",
    "last_name" => "",
    "user_name" => "",
    "email" => "",
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token!");
    }

    // Pre-fill form data
    foreach ($form_data as $key => &$value) {               // Foreach goes through every element in the array
        $value = trim($_POST[$key] ?? '');          // Trim removes redundant spaces. If input is nonexistent, empty string is used.
    }

    // Input validation
    if (empty($form_data["first_name"])) {
        $errors["first_name"] = "First name is required.";
    }
    if (empty($form_data["last_name"])) {
        $errors["last_name"] = "Last name is required.";
    }
    if (empty($form_data["user_name"])) {
        $errors["user_name"] = "User name is required.";
    }
    if (empty($form_data["email"]) || !filter_var($form_data["email"], FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "A valid email is required.";
    }

    // Password validation
    $password = $_POST["password"] ?? '';
    $password_confirm = $_POST["password_confirm"] ?? '';
    if (empty($password)) {
        $errors["password"] = "Password is required.";
    } elseif ($password !== $password_confirm) {
        $errors["password_confirm"] = "Passwords do not match.";
    }

    // If no errors, proceed with saving to the database
    if (empty($errors)) {
        // Database connection
        require_once 'db_connection.php';

        $stmt = $conn->prepare("SELECT id FROM users WHERE user_name = ? OR email = ?");    // Prepare SQL statement to check if user or mail already exists
        $stmt->bind_param("ss", $form_data["user_name"], $form_data["email"]);  // Bind input parameters to statement
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors["user_name"] = "This username or email is already registered.";
        }
        $stmt->close();

        if (empty($errors)) {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);     // Hash password by method BCRYPT
            $role = 'registered_user';
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, user_name, email, password_hash, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $form_data["first_name"], $form_data["last_name"], $form_data["user_name"], $form_data["email"], $password_hash, $role);

            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                unset($_SESSION['csrf_token']); // Remove token only after successful registration
                header("Location: registration_success.php");
                exit();
            } else {
                echo "Registration error: " . $stmt->error;
            }
        }
    }
}
?>

<!-- JavaScript for password validation -->
<script>
    function validatePassword() {
        const password = document.querySelector('input[name="password"]').value;
        const passwordConfirm = document.querySelector('input[name="password_confirm"]').value;
        const errorMessage = document.getElementById('password-error');

        if (password !== passwordConfirm)   // zkontroluj, jestli to takhle funguje
            errorMessage.textContent = "Passwords do not match.";
        } else {
            errorMessage.textContent = ""; // Clear error message if passwords match
        }
    }
                                                                    // tu opravit DOMContentLoaded - script hodit na konec kodu (dle Duska)
    document.addEventListener('DOMContentLoaded', function() {          // addEventListener starts when user starts typing into the password field
        document.querySelector('input[name="password"]').addEventListener('input', validatePassword);
        document.querySelector('input[name="password_confirm"]').addEventListener('input', validatePassword);
    });
</script>

<!-- Styling for error highlighting -->
<style>
    .error { color: red; font-size: 0.9em; }
    .input-error { border-color: red; }
</style>

<!-- Form with pre-filled values and error messages -->
<form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <!-- In HTML if input is valid, then $form_data, else empty string -->
    First Name:
    <input type="text" name="first_name" value="<?php echo htmlspecialchars($form_data['first_name']); ?>" class="<?php echo isset($errors['first_name']) ? 'input-error' : ''; ?>">
    <span class="error"><?php echo $errors["first_name"] ?? ''; ?></span>
    <br>

    Last Name:
    <input type="text" name="last_name" value="<?php echo htmlspecialchars($form_data['last_name']); ?>" class="<?php echo isset($errors['last_name']) ? 'input-error' : ''; ?>">
    <span class="error"><?php echo $errors["last_name"] ?? ''; ?></span>
    <br>

    User Name:
    <input type="text" name="user_name" value="<?php echo htmlspecialchars($form_data['user_name']); ?>" class="<?php echo isset($errors['user_name']) ? 'input-error' : ''; ?>">
    <span class="error"><?php echo $errors["user_name"] ?? ''; ?></span>
    <br>

    Email:
    <input type="email" name="email" value="<?php echo htmlspecialchars($form_data['email']); ?>" class="<?php echo isset($errors['email']) ? 'input-error' : ''; ?>">
    <span class="error"><?php echo $errors["email"] ?? ''; ?></span>
    <br>

    Password:
    <input type="password" name="password" class="<?php echo isset($errors['password']) ? 'input-error' : ''; ?>">
    <span class="error"><?php echo $errors["password"] ?? ''; ?></span>
    <br>

    Confirm Password:
    <input type="password" name="password_confirm" class="<?php echo isset($errors['password_confirm']) ? 'input-error' : ''; ?>">
    <span class="error" id="password-error"><?php echo $errors["password_confirm"] ?? ''; ?></span>
    <br>

    <input type="submit" value="Register">
</form>