<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if user is not logged in
if (empty($_SESSION["user_id"])) {
    $_SESSION["redirect_to"] = $_SERVER["REQUEST_URI"];
    header("Location: login.php");
    exit();
}

// Redirect to books page if user is not an admin
if ($_SESSION["user_role"] !== "admin") {
    header("Location: profile.php");
    exit();
}

require_once 'src/db_connection.php';

if (isset($_GET["userId"]) && intval($_GET["userId"]) > 0) {
    $user_id = intval($_GET["userId"]);
    $sql = "SELECT id, user_name, first_name, last_name, email, role FROM users WHERE id = :userId";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':userId', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch();
    if (!$user) {
        die("User not found.");
    }
} else {
    die("Invalid user ID.");
}

$first_name = $last_name = $user_name = $email = $password = $repassword = "";
$errors = [
    'first_name' => '',
    'last_name' => '',
    'user_name' => '',
    'email' => '',
    'role' => '',
    'password' => '',
    'repassword' => ''
];

// Function to validate email duplicity
function validate_email($email, $user_id, $pdo)
{
    $sql = "SELECT COUNT(id) FROM users WHERE email = :email AND id != :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email, ':user_id' => $user_id]);
    return $stmt->fetchColumn();
}

// Function to validate username duplicity
function validate_username($user_name, $user_id, $pdo)
{
    $sql = "SELECT COUNT(id) FROM users WHERE user_name = :user_name AND id != :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_name' => $user_name, ':user_id' => $user_id]);
    return $stmt->fetchColumn();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token! <br>" . $_POST['csrf_token'] . "<br>" . $_SESSION['csrf_token']);
    }
    if (isset($_POST["save_changes"])) {
        $first_name = trim($_POST["first_name"]);
        $last_name = trim($_POST["last_name"]);
        $user_name = trim($_POST["user_name"]);
        $email = trim($_POST["email"]);
        $password = trim($_POST["password"]);
        $repassword = trim($_POST["repassword"]);
        $role = trim($_POST["role"]);

        // Validate that fields are not empty
        if (empty($user_name)) $errors["user_name"] = "Username is required.";
        if (empty($first_name)) $errors["first_name"] = "First name is required.";
        if (empty($last_name)) $errors["last_name"] = "Last name is required.";

        // Validate email format
        if (empty($email)) {
            $errors["email"] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/', $email)) {
            $errors["email"] = "Invalid email address.";
        }
        // Check for email duplicity
        if (!empty($email) && validate_email($email, $user_id, $conn) > 0) {
            $errors['email'] = "Email is already registered with a different account.";
        }
        if (empty($role)) {
            $errors['role'] = "Role is required.";
        }
        // Validate role
        if (!in_array($role, ['registered_user', 'admin'])) {
            $errors['role'] = "Invalid role.";
        }

        // Validate email format if email is filled in
        if (!empty($email) && (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/', $email))) {
            $errors['email'] = "Invalid email address.";
        }

        // Check for username duplicity
        if (!empty($user_name) && validate_username($user_name, $user_id, $conn) > 0) {
            $errors['user_name'] = "Username is already taken.";
        }

        // Validate password length
        if (!empty($password) && strlen($password) < 6) {
            $errors['password'] = "Password must be at least 6 characters long.";
        }

        if ($password !== $repassword) {
            $errors['repassword'] = "Passwords do not match.";
        }

        if (empty($errors['user_name']) && empty($errors['email']) && empty($errors['password']) && empty($errors['repassword'])) {
            $stmt = $conn->prepare("UPDATE users SET first_name = :first_name, last_name = :last_name, user_name = :user_name, email = :email, role = :role WHERE id = :user_id");
            $stmt->bindValue(':first_name', $first_name, PDO::PARAM_STR);
            $stmt->bindValue(':last_name', $last_name, PDO::PARAM_STR);
            $stmt->bindValue(':user_name', $user_name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':role', $role, PDO::PARAM_STR);

            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt->execute();

                $stmt = $conn->prepare("UPDATE users SET password_hash = :password_hash WHERE id = :user_id");
                $stmt->bindValue(':password_hash', $hashed_password, PDO::PARAM_STR);
                $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            }

            if ($stmt->execute()) {
                header("Location: admin.php");
                exit();
            } else {
                echo "Error updating profile.";
            }
        }
    } elseif (isset($_POST["delete_account"])) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :user_id");
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: admin.php");
            exit();
        } else {
            echo "Error deleting account.";
        }
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
            <h1>Edit Profile</h1>
            <br>
            <div class="form-wrapper">
                <form action="#" method="post" enctype="multipart/form-data" class="my_form">
                    <fieldset>
                        <legend>Please update your info:</legend>
                        <br>
                        <label for="first_name">First name:</label>
                        <input class="form-input <?php echo !empty($errors['first_name']) ? 'error-border' : ''; ?>"
                               id="first_name" type="text" name="first_name" placeholder="John"
                               value="<?php echo htmlspecialchars(isset($_POST['first_name']) ? $_POST['first_name'] : $user['first_name']); ?>">
                        <span class="error" id="first_name_error"><?php echo $errors['first_name']; ?></span>

                        <label for="last_name">Last name:</label>
                        <input class="form-input <?php echo !empty($errors['last_name']) ? 'error-border' : ''; ?>"
                               id="last_name" type="text" name="last_name" placeholder="Smith"
                               value="<?php echo htmlspecialchars(isset($_POST['last_name']) ? $_POST['last_name'] : $user['last_name']); ?>">
                        <span class="error" id="last_name_error"><?php echo $errors['last_name']; ?></span>

                        <label for="user_name">User name:</label>
                        <input class="form-input <?php echo !empty($errors['user_name']) ? 'error-border' : ''; ?>"
                               id="user_name" type="text" name="user_name" placeholder="RolkieTolkie"
                               value="<?php echo htmlspecialchars(isset($_POST['user_name']) ? $_POST['user_name'] : $user['user_name']); ?>">
                        <span class="error" id="user_name_error"><?php echo $errors['user_name']; ?></span>

                        <label for="email">Email:</label>
                        <input class="form-input <?php echo !empty($errors['email']) ? 'error-border' : ''; ?>"
                               id="email" type="email" name="email"
                               value="<?php echo htmlspecialchars(isset($_POST['email']) ? $_POST['email'] : $user['email']); ?>">
                        <span class="error" id="email_error"><?php echo $errors['email']; ?></span>

                        <label for="role">Role:</label>
                        <select class="form-select <?php echo !empty($errors['role']) ? 'error-border' : ''; ?>"
                                id="role" name="role">
                            <option value="registered_user" <?php if ($user["role"] == "registered_user") echo "selected" ?>>
                                User
                            </option>
                            <option value="admin" <?php if ($user["role"] == "admin") echo "selected" ?>>
                                Admin
                            </option>
                        </select>
                        <span class="error" id="role_error"><?php echo $errors['role']; ?></span>

                        <label for="password">New Password:</label>
                        <input class="form-input <?php echo !empty($errors['password']) ? 'error-border' : ''; ?>"
                               id="password" type="password" name="password" placeholder="at least 6 characters">
                        <span class="error" id="password_error"><?php echo $errors['password']; ?></span>


                        <label for="repassword">Re-enter New Password:</label>
                        <input class="form-input <?php echo !empty($errors['repassword']) ? 'error-border' : ''; ?>"
                               id="repassword" type="password" name="repassword">
                        <span class="error" id="repassword_error"><?php echo $errors['repassword']; ?></span>

                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                        <div class="button-container">
                            <button class="button" type="submit" name="save_changes">Save changes</button>
                            <button class="button" type="submit" name="delete_account">Delete account</button>
                        </div>
                    </fieldset>
                </form>
                <br><br>
            </div>
        </article>
    </div>

<?php include 'footer.php'; ?>