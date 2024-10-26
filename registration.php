<?php
// Připojení k databázi
$servername = "localhost";
$username = "andy";
$password = "andy123";
$dbname = "ostruand";

// Vytvoření připojení
$conn = new mysqli($servername, $username, $password, $dbname);

// Kontrola připojení
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Inicializace proměnných pro chybové zprávy
$errors = [];

// Zpracování formuláře po odeslání
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Načtení a vyčištění vstupních dat
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $user_name = trim($_POST["user_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $password_confirm = $_POST["password_confirm"];

    // Validace formulářových dat
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

    // Kontrola, zda není user_name nebo email již v databázi
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE user_name = ? OR email = ?");
        $stmt->bind_param("ss", $user_name, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Tento user_name nebo e-mail je již registrován.";
        }
        $stmt->close();
    }

    // Pokud nejsou chyby, vložíme uživatele do databáze
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $role = 'registered_user'; // Defaultní role

        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, user_name, email, password_hash, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $first_name, $last_name, $user_name, $email, $password_hash, $role);

        if ($stmt->execute()) {
            echo "Registrace byla úspěšná!";
        } else {
            echo "Došlo k chybě při registraci: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // Výpis chyb
        foreach ($errors as $error) {
            echo htmlspecialchars($error) . "<br>";
        }
    }
}

$conn->close();
?>

<!-- HTML formulář -->
<form method="POST" action="">
    Jméno: <input type="text" name="first_name" required><br>
    Příjmení: <input type="text" name="last_name" required><br>
    User name: <input type="text" name="user_name" required><br>
    E-mail: <input type="email" name="email" required><br>
    Heslo: <input type="password" name="password" required><br>
    Potvrzení hesla: <input type="password" name="password_confirm" required><br>
    <input type="submit" value="Registrovat">
</form>
