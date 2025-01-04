<?php
session_start();
$redirect = isset($_GET["redirect"]) ? $_GET["redirect"] : "index.php";
// Database connection
require_once 'db_connection.php';

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
        $stmt = $conn->prepare("SELECT id, role, user_name, password_hash FROM users WHERE user_name = :user_name");
        $stmt->bindValue(":user_name", $user_name, PDO::PARAM_STR);
        //$stmt->bindValue(":email", $user, PDO::PARAM_STR);
        $stmt->execute();
        $res = $stmt->fetch();

        if (isset($res)) {
            // Fetch password hash and verify
            if (password_verify($password, $res["password_hash"])) {
                // Successful login, create session
                $_SESSION["user_id"] = $res["id"];
                $_SESSION["user_name"] = $res["user_name"];
                $_SESSION["user_role"] = $res["role"];
                //echo "Login successful! Welcome, " . htmlspecialchars($_SESSION["user_name"]) . ".";
                header("Location: $redirect");
                exit();

            } else {
                $errors["password"] = "Incorrect password.";
            }
        } else {
            $errors["user_name"] = "No user found with that username or email.";
        }
    }
}


?>

<?php include 'header.php'; ?>
<div id="content">
    <article id="main-widest">
        <h1>Log in</h1>
        <br>
        <div class="form-wrapper">
            <div class="form-container">
                <form action="#" method="post" enctype="multipart/form-data" class="my_form">
                    <fieldset>
                        <legend>Please fill in your login info:</legend>
                        <br>
                        <label for="user_name">Nickname:</label>
                        <input class="form-input" id="user_name" type="text" name="user_name" placeholder="Bookworm125"
                               required>
                        <br><br>
                        <label for="password">Password:</label>
                        <input class="form-input" id="password" type="password" name="password" placeholder="*******"
                               required>
                        <br><br>
                        <div class="button-container">
                            <button class='button' type="submit">Log in</button>
                            <button class='button' type="reset">Reset form</button>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="link-div">
                <p>Don't have an account yet? Click <a href="register.php">here!</a></p>
            </div>
            <br><br>
        </div>
    </article>
</div>


<?php include 'footer.php'; ?>
