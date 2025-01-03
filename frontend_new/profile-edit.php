<?php
session_start();

require_once 'db_connection.php';

if (empty($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$sql = "SELECT first_name, last_name, user_name, email FROM users WHERE id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
if ($stmt->execute()) {
    $user = $stmt->fetch();
} else {
    die("Error fetching user data.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["save_changes"])) {
        $first_name = trim($_POST["fName"]);
        $last_name = trim($_POST["lName"]);
        $user_name = trim($_POST["user_name"]);
        $email = trim($_POST["email"]);
        $password = trim($_POST["password"]);
        $repassword = trim($_POST["repassword"]);

        if ($password !== $repassword) {
            die("Passwords do not match.");
        }

        $stmt = $conn->prepare("UPDATE users SET first_name = :first_name, last_name = :last_name, user_name = :user_name, email = :email WHERE id = :user_id");
        $stmt->bindValue(':first_name', $first_name, PDO::PARAM_STR);
        $stmt->bindValue(':last_name', $last_name, PDO::PARAM_STR);
        $stmt->bindValue(':user_name', $user_name, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->execute();

            $stmt = $conn->prepare("UPDATE users SET password_hash = :password_hash WHERE id = :user_id");
            $stmt->bindValue(':password_hash', $hashed_password, PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        }

        if ($stmt->execute()) {
            header("Location: profile.php");
            exit();
        } else {
            echo "Error updating profile.";
        }
    } elseif (isset($_POST["delete_account"])) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :user_id");
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            session_destroy();
            header("Location: index.php");
            exit();
        } else {
            echo "Error deleting account.";
        }
    }
}
?>

<?php include 'header.php'; ?>

    <div id="content">
        <article id="main-widest">
            <h1>Edit Profile</h1>
            <br>
            <div class="form-wrapper">
                <form action="#" method="post" enctype="multipart/form-data" class="my_form">
                    <fieldset>
                        <legend>Please update your info:</legend>
                        <br>
                        <label for="fName">First name:</label>
                        <input class="form-input" id="fName" type="text" name="fName" placeholder="John" required value="<?php echo htmlspecialchars($user['first_name']); ?>">

                        <label for="lName">Last name:</label>
                        <input class="form-input" id="lName" type="text" name="lName" placeholder="Smith" required value="<?php echo htmlspecialchars($user['last_name']); ?>">

                        <label for="user_name">User name:</label>
                        <input class="form-input" id="user_name" type="text" name="user_name" placeholder="BookWorm125" required value="<?php echo htmlspecialchars($user['user_name']); ?>">

                        <label for="email">Email:</label>
                        <input class="form-input" id="email" type="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>">

                        <label for="password">New Password:</label>
                        <input class="form-input" id="password" type="password" name="password" placeholder="at least 6 characters">
                        <label for="repassword">Re-enter New Password:</label>
                        <input class="form-input" id="repassword" type="password" name="repassword">

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