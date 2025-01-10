<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
require_once 'src/db_connection.php';
require_once 'src/insert_image.php';

if (empty($_SESSION["user_id"])) {
    $_SESSION["redirect_to"] = $_SERVER["REQUEST_URI"];
    header("Location: login.php");
    exit();
}

if ($_SESSION["user_role"] !== "admin") {
    header("Location: profile.php");
    exit();
}

if (isset($_GET["book_id"])) {
    $book_id = $_GET["book_id"];
} else {
    die("Book not specified.");
}

// Fetch book details from the database
try {
    $sql = "SELECT * FROM books WHERE id = :book_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":book_id", $book_id);
    $stmt->execute();
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        die("Book not found.");
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Function to validate and sanitize input
function validate_input($data)
{
    $data = trim($data); // Remove whitespace from the beginning and end
    return $data;
}

// Initialize variables to store form values and error messages
$title = $book['title'];
$isbn = $book['isbn'];
$author = $book['author'];
$literary_genre = $book['literary_genre'];
$fictional_genre = $book['fiction_genre'];
$sDesc = $book['description_short'];
$lDesc = $book['description_long'];
$date = $book['release_date'];
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
    if (isset($_POST["save_changes"])) {
        // Validate and sanitize form fields
        $title = trim($_POST["title"]);
        $isbn = trim($_POST["isbn"]);
        $author = trim($_POST["author"]);
        $literary_genre = trim($_POST["literary_genre"]);
        $fictional_genre = trim($_POST["fictional_genre"]);
        $sDesc = trim($_POST["sDesc"]);
        $lDesc = trim($_POST["lDesc"]);
        $date = trim($_POST["date"]);

        // Validate required fields
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
        // Check if ISBN already exists, excluding the current book
        $stmt = $conn->prepare("SELECT COUNT(*) FROM books WHERE isbn = :isbn AND id != :book_id");
        $stmt->bindValue(':isbn', $isbn);
        $stmt->bindValue(':book_id', $book_id);
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
        // If no errors, proceed to update data in the database
        if (empty(array_filter($errors))) {
            try {
                $sql = "UPDATE books SET title = :title, isbn = :isbn, author = :author, literary_genre = :literary_genre, fiction_genre = :fictional_genre, description_short = :sDesc, description_long = :lDesc, release_date = :date WHERE id = :book_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(":title", $title);
                $stmt->bindValue(":isbn", $isbn);
                $stmt->bindValue(":author", $author);
                $stmt->bindValue(":literary_genre", $literary_genre);
                $stmt->bindValue(":fictional_genre", $fictional_genre);
                $stmt->bindValue(":sDesc", $sDesc);
                $stmt->bindValue(":lDesc", $lDesc);
                $stmt->bindValue(":date", $date);
                $stmt->bindValue(":book_id", $book_id);
                $stmt->execute();

                // Handle image upload
                $result = handle_image_upload($book_id, $title, $_FILES["cover"]);
                if ($result !== true) {
                    $errors['cover'] = implode("<br>", $result["errors"]);
                }

                // Redirect to book details page
                header("Location: book_detail.php?bookid=" . $book_id);
                exit();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    } elseif (isset($_POST["delete_book"])) {
        // Delete book logic
        try {
            $sql = "DELETE FROM books WHERE id = :book_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(":book_id", $book_id);
            $stmt->execute();

            // Redirect to the book list page after deletion
            header("Location: books.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

?>
<?php include 'header.php'; ?>
    <div id="content">
        <script src="js/form_validation_book.js" defer></script>
        <article id="main-widest">
            <h1>Edit book</h1>
            <br>
            <div class="form-wrapper">
                <form id="edit_books" class="my_form"
                      action="book_edit.php?book_id=<?php echo htmlspecialchars($book_id); ?>"
                      enctype="multipart/form-data" method="POST">
                    <fieldset>
                        <legend>Please fill in info about the book:</legend>
                        <div class="form_group">
                            <label for="title">Title:</label>
                            <input class="form-input <?php echo !empty($errors['title']) ? 'error-border' : ''; ?>"
                                   id="title" type="text" name="title" placeholder="The Hobbit"
                                   value="<?php echo htmlspecialchars($title); ?>" maxlength="255">
                            <span class="error" id="title_error"><?php echo $errors['title']; ?></span>
                        </div>
                        <div class="form_group">
                            <label for="isbn">ISBN:</label>
                            <input class="form-input <?php echo !empty($errors['isbn']) ? 'error-border' : ''; ?>"
                                   type="text" id="isbn" name="isbn" placeholder="10 or 13 digits"
                                   pattern="\d{10}|\d{13}"
                                   value="<?php echo htmlspecialchars($isbn); ?>"
                                   data-allowed="<?php echo htmlspecialchars($isbn); ?>">
                            <span class="error" id="isbn_error"><?php echo $errors['isbn']; ?></span>
                        </div>
                        <div class="form_group">
                            <label for="author">Author:</label>
                            <input class="form-input <?php echo !empty($errors['author']) ? 'error-border' : ''; ?>"
                                   id="author" type="text" name="author" placeholder="J. R. R. Tolkien"
                                   value="<?php echo htmlspecialchars($author); ?>">
                            <span class="error" id="author_error"><?php echo $errors['author']; ?></span>
                            <div class="form_group">
                                <label for="literary_genre">Literary Genre:</label>
                                <select class="form-select <?php echo !empty($errors['literary_genre']) ? 'error-border' : ''; ?>"
                                        id="literary_genre" name="literary_genre">
                                    <option value="prose" <?php if ($literary_genre == 'prose') echo 'selected'; ?>>
                                        Prose
                                    </option>
                                    <option value="poetry" <?php if ($literary_genre == 'poetry') echo 'selected'; ?>>
                                        Poetry
                                    </option>
                                    <option value="drama" <?php if ($literary_genre == 'drama') echo 'selected'; ?>>
                                        Drama
                                    </option>
                                </select>
                                <span class="error"
                                      id="literary_genre_error"><?php echo $errors['literary_genre']; ?></span>
                            </div>
                            <div class="form_group">
                                <label for="fictional_genre">Fiction Genre:</label>
                                <select class="form-select <?php echo !empty($errors['fictional_genre']) ? 'error-border' : ''; ?>"
                                        id="fictional_genre" name="fictional_genre">
                                    <option value="romance" <?php if ($fictional_genre == 'romance') echo 'selected'; ?>>
                                        Romance
                                    </option>
                                    <option value="scifi" <?php if ($fictional_genre == 'scifi') echo 'selected'; ?>>
                                        Sci-Fi
                                    </option>
                                    <option value="fantasy" <?php if ($fictional_genre == 'fantasy') echo 'selected'; ?>>
                                        Fantasy
                                    </option>
                                    <option value="horror" <?php if ($fictional_genre == 'horror') echo 'selected'; ?>>
                                        Horror
                                    </option>
                                    <option value="other" <?php if ($fictional_genre == 'other') echo 'selected'; ?>>
                                        Other
                                    </option>
                                </select>
                                <span class="error"
                                      id="fiction_genre_error"><?php echo $errors['fictional_genre']; ?></span>
                            </div>
                            <div class="form_group">
                                <label for="sDesc">Short description:</label>
                                <input class="form-input <?php echo !empty($errors['sDesc']) ? 'error-border' : ''; ?>"
                                       id="sDesc" type="text" name="sDesc"
                                       value="<?php echo htmlspecialchars($sDesc); ?>">
                                <span class="error" id="sDesc_error"><?php echo $errors['sDesc']; ?></span>
                            </div>
                            <div class="form_group">
                                <label for="lDesc">Long description:</label>
                                <textarea
                                        class="form-input <?php echo !empty($errors['lDesc']) ? 'error-border' : ''; ?>"
                                        id="lDesc" name="lDesc"><?php echo htmlspecialchars($lDesc); ?></textarea>
                                <span class="error" id="lDesc_error"><?php echo $errors['lDesc']; ?></span>
                            </div>
                            <div class="form_group">
                                <label for="date">Release date:</label>
                                <input class="form-input <?php echo !empty($errors['date']) ? 'error-border' : ''; ?>"
                                       id="date"
                                       type="date" name="date" value="<?php echo htmlspecialchars($date); ?>">
                                <span class="error" id="date_error"><?php echo $errors['date']; ?></span>
                                <div class="form_group">
                                    <label for="cover">Upload Book Cover:</label>
                                    <input type="file" class="form-file" id="cover" name="cover" accept="image/*">
                                    <span class="error"><?php echo $errors['cover']; ?></span>
                                </div>
                                <input type="hidden" name="added_by"
                                       value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
                                <br><br>
                                <div class="button-container">
                                    <button type="submit" class="button" name="save_changes">Update book</button>
                                    <button type="submit" class="button" name="delete_book">Delete book</button>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </article>
    </div>


<?php include 'footer.php'; ?>