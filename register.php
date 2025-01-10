<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'src/db_connection.php';

if (isset($_SESSION["user_id"])) {
    header("Location: profile.php");
    exit();
}

// Initialize errors and pre-filled form data
$first_name = $last_name = $user_name = $email = $password = $repassword = "";
$errors = [
    'user_name' => '',
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'password' => '',
    'repassword' => '',
    'agreed' => ''
];

// Function to validate username duplicity
function validate_username($user_name, $pdo)
{
    $sql = "SELECT COUNT(id) FROM users WHERE user_name = :user_name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_name' => $user_name]);
    return $stmt->fetchColumn();
}

// Function to validate email duplicity
function validate_email($email, $pdo)
{
    $sql = "SELECT COUNT(id) FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    return $stmt->fetchColumn();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token! <br>" . $_POST['csrf_token'] . "<br>" . $_SESSION['csrf_token']);
    }

    $user_name = trim($_POST["user_name"]);
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $repassword = $_POST["repassword"];
    $agreed = isset($_POST["agreed"]) ? $_POST["agreed"] : '';


    // Validate that fields are not empty
    if (empty($user_name)) $errors["user_name"] = "Username is required. PHP";
    if (empty($first_name)) $errors["first_name"] = "First name is required. PHP";
    if (empty($last_name)) $errors["last_name"] = "Last name is required. PHP";

    // Validate email format
    if (empty($email)) {
        $errors["email"] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/', $email)) {
        $errors["email"] = "Invalid email address.";
    }
    if (!empty($email) && validate_email($email, $conn) > 0) {
        $errors['email'] = "Email is already registered with a different account.";
    }

    if (empty($password)) {
        $errors["password"] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors["password"] = "Password must be at least 6 characters.";
    }
    if (empty($repassword)) {
        $errors["repassword"] = "Please re-enter password.";
    } elseif ($password !== $repassword) {
        $errors["repassword"] = "Passwords do not match.";
    }
    if (empty($agreed)) $errors["agreed"] = "You must agree to the terms.";

    // Check for email duplicity
    if (validate_email($email, $conn) > 0) {
        $errors["email"] = "Email is already registered.";
    }

    if (validate_username($user_name, $conn) > 0) {
        $errors["user_name"] = "Username already exists.";
    }
    if (count(array_filter($errors)) == 0) {
        $sql = "INSERT INTO users (first_name, last_name, user_name, email, password_hash, role) VALUES (:first_name, :last_name, :user_name, :email, :password, :role)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':user_name' => $user_name,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':role' => 'registered_user'
        ]);

        $user_id = $conn->lastInsertId(); // Define $user_id here
        $_SESSION["user_id"] = $user_id;
        $_SESSION["user_name"] = $user_name;
        $_SESSION["user_role"] = 'registered_user';
        header("Location: profile.php");
        exit();
    }
}

// Generate CSRF token
$csrf_token = bin2hex(random_bytes(16));
$_SESSION["csrf_token"] = $csrf_token;
?>

<?php include 'header.php'; ?>
<script src="js/form_validation_profile.js" defer></script>

<div id="content">
    <article id="main-widest">
        <h1>Registration</h1>
        <br>
        <div class="form-wrapper">
            <form action="#" method="post" enctype="multipart/form-data" class="my_form" id="registrationForm">
                <fieldset>
                    <legend>Please fill in info about yourself:</legend>
                    <label for="first_name">* First name:</label>
                    <input class="form-input <?php echo !empty($errors['first_name']) ? 'error-border' : ''; ?>"
                           id="first_name" type="text" name="first_name" placeholder="J. R. R."
                           value="<?php if (isset($_POST["first_name"])) {
                               echo htmlspecialchars($first_name);
                           } ?>">
                    <span class="error" id="first_name_error"><?php echo $errors['first_name']; ?></span>

                    <label for="last_name">* Last name:</label>
                    <input class="form-input <?php echo !empty($errors['last_name']) ? 'error-border' : ''; ?>"
                           id="last_name" type="text" name="last_name" placeholder="Tolkien"
                           value="<?php echo htmlspecialchars($last_name); ?>">
                    <span class="error" id="last_name_error"><?php echo $errors['last_name']; ?></span>


                    <label for="user_name">* Username:</label>
                    <input class="form-input <?php echo !empty($errors['user_name']) ? 'error-border' : ''; ?>"
                           id="user_name" type="text" name="user_name" placeholder="RolkieTolkie"
                           value="<?php if (isset($_POST["user_name"])) {
                               echo htmlspecialchars($user_name);
                           } ?>">
                    <span class="error" id="user_name_error"><?php echo $errors['user_name']; ?></span>

                    <label for="email">* Email:</label>
                    <input class="form-input <?php echo !empty($errors['email']) ? 'error-border' : ''; ?>" id="email"
                           type="email" name="email" placeholder="tolkien@books.com"
                           value="<?php if (isset($_POST["email"])) {
                               echo htmlspecialchars($email);
                           } ?>">
                    <span class="error" id="email_error"><?php echo $errors['email']; ?></span>

                    <label for="password">* Password:</label>
                    <input class="form-input  <?php echo !empty($errors['password']) ? 'error-border' : ''; ?>"
                           id="password" type="password" name="password" placeholder="at least 6 characters">
                    <span class="error" id="password_error"><?php echo $errors['password']; ?></span>

                    <label for="repassword">* Re-enter password:</label>
                    <input class="form-input  <?php echo !empty($errors['repassword']) ? 'error-border' : ''; ?>"
                           id="repassword" type="password" name="repassword">
                    <span class="error" id="repassword_error"><?php echo $errors['repassword']; ?></span>

                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                    <label class="checkbox-label <?php echo !empty($errors['agreed']) ? 'error-border' : ''; ?>">
                        <input type="checkbox" name="agreed" value="yes" checked>
                        * I agree to the BookNook Terms of Service and Privacy Policy
                    </label>
                    <p class="error" id="agreed_error"><?php echo $errors['agreed']; ?></p>

                    <br>
                    <p>* mandatory field</p>
                    <br><br>

                    <div class="button-container">
                        <button class="button" type="submit" name="save_changes">Register</button>
                        <button class="button" type="reset">Reset form</button>
                    </div>
                </fieldset>
            </form>
            <div class="link-div">
                <p>Already have an account? Click <a href="login.php">here!</a></p>
            </div>
            <br><br>
        </div>
    </article>
</div>

<?php include 'footer.php'; ?>
