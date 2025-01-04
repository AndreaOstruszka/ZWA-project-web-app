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
        $value = htmlspecialchars(trim($_POST[$key] ?? ''), ENT_QUOTES, 'UTF-8');          // Trim removes redundant spaces. If input is nonexistent, empty string is used.
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
            var_dump($password, bin2hex($password), $password_hash); // Debugging line
            $role = 'registered_user';
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, user_name, email, password_hash, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $form_data["first_name"], $form_data["last_name"], $form_data["user_name"], $form_data["email"], $password_hash, $role);

            //$sql = "INSERT INTO users (Username, Name, Surname, Email, PasswordHash) VALUES (?, ?, ?, ?, ?)";
            //$stmt = $conn->prepare($sql);
            //$stmt->bind_param("sssss", $form_data["user_name"], $form_data["first_name"], $form_data["last_name"], $form_data["email"], $password_hash);

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

<?php include 'header.php'; ?>

<div id="content">
    <article id="main-widest">
        <h1>Registration</h1>
        <br>
        <div class="form-wrapper">
            <form action="#" method="post" enctype="multipart/form-data" class="my_form">
                <legend>Please fill in info about yourself:</legend>
                <label for="fName">First name:</label>
                <input class="form-input" id="fName" type="text" name="fName" placeholder="John" required>

                <label for="lName">Last name:</label>
                <input class="form-input" id="lName" type="text" name="lName" placeholder="Doe" required>

                <label for="nickname">Nickname:</label>
                <input class="form-input" id="nickname" type="text" name="nickname" placeholder="BookWorm125" required>

                <label for="email">Email:</label>
                <input class="form-input" id="email" type="email" name="email" value="@" required>

                <label for="password">Password:</label>
                <input class="form-input" id="password" type="password" name="password" placeholder="at least 6 characters" required>

                <label for="repassword">Re-enter password:</label>
                <input class="form-input" id="repassword" type="password" name="repassword" required>

                <label class="checkbox-label">
                    <input type="checkbox" name="agreed" value="yes" checked required>
                    I agree to the BookNook Terms of Service and Privacy Policy
                </label>

                <div class="button-container">
                    <button class="button" type="submit">Register</button>
                    <button class="button" type="reset">Reset form</button>
                </div>
            </form>
            <div class="link-div">
                <p>Already have an account? Click <a href="login.php">here!</a></p>
            </div>
            <br><br>
        </div>
    </article>
</div>

<?php include 'footer.php'; ?>
