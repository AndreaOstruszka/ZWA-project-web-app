<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
require_once 'src/db_connection.php';
require_once 'src/insert_image.php';

if ($_SESSION["user_role"] !== "admin") {
    header("Location: books.php");
    exit();
}

// Initialize variables to store form values and error messages
$title = $isbn = $author = $literary_genre = $fictional_genre = $sDesc = $lDesc = $date = "";
$errors = [
    'title' => '',
    'isbn' => '',
    'author' => '',
    'literary_genre' => '',
    'fictional_genre' => '',
    'sDesc' => '',
    'lDesc' => '',
    'date' => '',
    'cover' => ''
];

// Define acceptable values for genres
$acceptable_literary_genres = ['prose', 'poetry', 'drama'];
$acceptable_fiction_genres = ['romance', 'scifi', 'fantasy', 'horror', 'other'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token! <br>" . $_POST['csrf_token'] . "<br>" . $_SESSION['csrf_token']);
    }

    // Validate and sanitize form fields
    $title = trim($_POST["title"]);
    $isbn = trim($_POST["isbn"]);
    $author = trim($_POST["author"]);
    $literary_genre = trim($_POST["literary_genre"]);
    $fictional_genre = trim($_POST["fictional_genre"]);
    $sDesc = trim($_POST["sDesc"]);
    $lDesc = trim($_POST["lDesc"]);
    $date = trim($_POST["date"]);

    // Validate that fields are not empty
    if (empty($title)) $errors['title'] = "Title is required.";
    if (empty($isbn)) $errors['isbn'] = "ISBN is required.";
    if (empty($author)) $errors['author'] = "Author is required.";
    if (empty($literary_genre)) $errors['literary_genre'] = "Literary genre is required.";
    if (empty($fictional_genre)) $errors['fictional_genre'] = "Fiction genre is required.";
    if (empty($sDesc)) $errors['sDesc'] = "Short description is required.";
    if (empty($lDesc)) $errors['lDesc'] = "Long description is required.";
    if (empty($date)) $errors['date'] = "Release date is required.";

    // Validate ISBN format
    if (!empty($isbn) && (!ctype_digit($isbn) || (strlen($isbn) !== 10 && strlen($isbn) !== 13))) {
        $errors['isbn'] = "ISBN must be a 10 or 13 digit number.";
    }
    // Check if ISBN already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM books WHERE isbn = :isbn");
    $stmt->bindValue(':isbn', $isbn);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    if ($count > 0) {
        $errors['isbn'] = "ISBN already exists.";
    }


    // Validate genres
    if (!in_array($literary_genre, $acceptable_literary_genres)) {
        $errors['literary_genre'] = "Invalid literary genre.";
    }
    if (!in_array($fictional_genre, $acceptable_fiction_genres)) {
        $errors['fictional_genre'] = "Invalid fiction genre.";
    }

    // Check if ISBN already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM books WHERE isbn = :isbn");
    $stmt->bindValue(':isbn', $isbn);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    if ($count > 0) {
        $errors['isbn'] = "ISBN already exists.";
    }

    // If no errors, proceed to insert data into the database
    if (empty(array_filter($errors))) {
        try {
            $sql = "INSERT INTO books (title, isbn, author, literary_genre, fiction_genre, description_short, description_long, added_by, release_date) VALUES (:title, :isbn, :author, :literary_genre, :fictional_genre, :sDesc, :lDesc, :added_by, :date)";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(":title", $title);
            $stmt->bindValue(":isbn", $isbn);
            $stmt->bindValue(":author", $author);
            $stmt->bindValue(":literary_genre", $literary_genre);
            $stmt->bindValue(":fictional_genre", $fictional_genre);
            $stmt->bindValue(":sDesc", $sDesc);
            $stmt->bindValue(":lDesc", $lDesc);
            $stmt->bindValue(":date", $date);
            $stmt->bindValue(":added_by", $_SESSION["user_id"]);
            $stmt->execute();
            $book_id = $conn->lastInsertId();

            // Handle image upload
            $result = handle_image_upload($book_id, $title, $_FILES["cover"]);
            if ($result !== true) {
                $errors['cover'] = implode("<br>", $result["errors"]);
            }

            // Redirect to profile page
            header("Location: profile.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

// Generate CSRF token
$csrf_token = bin2hex(random_bytes(16));
$_SESSION["csrf_token"] = $csrf_token;
?>

<?php include 'header.php'; ?>

<div id="content">
    <script src="js/form_validation_book.js" defer></script>
    <article id="main-widest">
        <h1>Add new book</h1>
        <br>
        <div class="form-wrapper">
            <form id="add_books" action="#" enctype="multipart/form-data" method="POST" class="my_form">
                <fieldset>
                    <legend>Please fill in info about the book:</legend>
                    <div class="form_group">
                        <label for="title">* Title:</label>
                        <input class="form-input <?php echo !empty($errors['title']) ? 'error-border' : ''; ?>"
                               id="title"
                               type="text" name="title" placeholder="The Hobbit" maxlength="255"
                               value="<?php if (isset($_POST["title"])) {
                                   echo htmlspecialchars($title);
                               } ?>">
                        <span class="error" id="title_error"><?php echo $errors['title']; ?></span>
                    </div>
                    <div class="form_group">
                        <label for="isbn">* ISBN:</label>
                        <input class="form-input <?php echo !empty($errors['isbn']) ? 'error-border' : ''; ?>"
                               type="text"
                               id="isbn" name="isbn" placeholder="10 or 13 digits" pattern="\d{10}|\d{13}"
                               value="<?php if (isset($_POST["isbn"])) {
                                   echo htmlspecialchars($isbn);
                               } ?>">
                        <span class="error" id="isbn_error"><?php echo $errors['isbn']; ?></span>
                    </div>
                    <div class="form_group">
                        <label for="author">* Author:</label>
                        <input class="form-input <?php echo !empty($errors['author']) ? 'error-border' : ''; ?>"
                               id="author"
                               type="text" name="author" placeholder="J. R. R. Tolkien"
                               value="<?php if (isset($_POST["author"])) {
                                   echo htmlspecialchars($author);
                               } ?>">
                        <span class="error" id="author_error"><?php echo $errors['author']; ?></span>
                    </div>
                    <div class="form_group">
                        <label for="literary_genre">* Literary Genre:</label>
                        <select class="form-select <?php echo !empty($errors['fictional_genre']) ? 'error-border' : ''; ?>"
                                id="literary_genre" name="literary_genre">
                            <option value="prose" <?php if (isset($_POST['fictional_genre']) && $fictional_genre == 'prose') echo 'selected'; ?>>
                                Prose
                            </option>
                            <option value="poetry" <?php if (isset($_POST['fictional_genre']) && $fictional_genre == 'poetry') echo 'selected'; ?>>
                                Poetry
                            </option>
                            <option value="drama" <?php if (isset($_POST['fictional_genre']) && $fictional_genre == 'drama') echo 'selected'; ?>>
                                Drama
                            </option>
                        </select>
                        <span class="error" id="literary_genre_error"><?php echo $errors['literary_genre']; ?></span>
                    </div>
                    <div class="form_group">
                        <label for="fictional_genre">* Fiction Genre:</label>
                        <select class="form-select <?php echo !empty($errors['fictional_genre']) ? 'error-border' : ''; ?>"
                                id="fictional_genre" name="fictional_genre">
                            <option value="romance" <?php if (isset($_POST['fictional_genre']) && $fictional_genre == 'romance') echo 'selected'; ?>>
                                Romance
                            </option>
                            <option value="scifi" <?php if (isset($_POST['fictional_genre']) && $fictional_genre == 'scifi') echo 'selected'; ?>>
                                Sci-Fi
                            </option>
                            <option value="fantasy" <?php if (isset($_POST['fictional_genre']) && $fictional_genre == 'fantasy') echo 'selected'; ?>>
                                Fantasy
                            </option>
                            <option value="horror" <?php if (isset($_POST['fictional_genre']) && $fictional_genre == 'horror') echo 'selected'; ?>>
                                Horror
                            </option>
                            <option value="other" <?php if (isset($_POST['fictional_genre']) && $fictional_genre == 'other') echo 'selected'; ?>>
                                Other
                            </option>
                        </select>
                        <span class="error" id="fiction_genre_error"><?php echo $errors['fictional_genre']; ?></span>
                    </div>

                    <div class="form_group">
                        <label for="sDesc">* Short description:</label>
                        <input class="form-input <?php echo !empty($errors['sDesc']) ? 'error-border' : ''; ?>"
                               id="sDesc"
                               type="text" name="sDesc" value="<?php if (isset($_POST["sDesc"])) {
                            echo htmlspecialchars($sDesc);
                        } ?>">
                        <span class="error" id="sDesc_error"><?php echo $errors['sDesc']; ?></span>
                    </div>

                    <div class="form_group">
                        <label for="lDesc">* Long description:</label>
                        <textarea class="form-input <?php echo !empty($errors['lDesc']) ? 'error-border' : ''; ?>"
                                  id="lDesc" name="lDesc"><?php if (isset($_POST["lDesc"])) {
                                echo htmlspecialchars($lDesc);
                            } ?></textarea>
                        <span class="error" id="lDesc_error"><?php echo $errors['lDesc']; ?></span>
                    </div>

                    <div class="form_group">
                        <label for="date">* Release date:</label>
                        <input class="form-input <?php echo !empty($errors['date']) ? 'error-border' : ''; ?>" id="date"
                               type="date" name="date" value="<?php if (isset($_POST["date"])) {
                            echo htmlspecialchars($date);
                        } ?>">
                        <span class="error" id="date_error"><?php echo $errors['date']; ?></span>
                    </div>

                    <div class="form_group">
                        <label for="cover">Upload Book Cover:</label>
                        <input type="file"
                               class="form-file <?php echo !empty($errors['cover']) ? 'error-border' : ''; ?>"
                               id="cover" name="cover" accept="image/*">
                        <span class="error" id="cover_error"><?php echo $errors['cover']; ?></span>
                    </div>

                    <!-- Hidden field for user ID -->
                    <input type="hidden" name="added_by" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">

                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                    <br>
                    <p>* mandatory field</p>
                    <br><br>

                    <div class="button-container">
                        <button type="submit" class="button">Add Book</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </article>
</div>

<?php include 'footer.php'; ?>
