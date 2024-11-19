<?php
// Database connection
require_once 'db_connection.php';

// Initialize error message variables
$errors = [];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {         // This block starts only if the form method is POST
    $book_id = intval($_POST["book_id"]);           // Intval ensures that book_id is an integer

    // Check if file was uploaded without errors
    if (isset($_FILES["book_cover"]) && $_FILES["book_cover"]["error"] == 0) {
        $allowed = ["jpg" => "image/jpeg", "png" => "image/png", "gif" => "image/gif"];
        $filename = $_FILES["book_cover"]["name"];
        $filetype = $_FILES["book_cover"]["type"];
        $filesize = $_FILES["book_cover"]["size"];

        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed)) {
            $errors[] = "Error: Please select a valid file format.";
        }

        // Verify file size - 5MB maximum
        if ($filesize > 5 * 1024 * 1024) {
            $errors[] = "Error: File size is larger than the allowed limit.";
        }

        // Verify MIME type of the file
        if (in_array($filetype, $allowed)) {
            // Check whether uploads directory exists, if not, create it
            if (!is_dir("uploads")) {
                mkdir("uploads", 0777, true);
            }

            // Move the uploaded file to the server
            $new_filename = "uploads/" . basename($filename);
            if (move_uploaded_file($_FILES["book_cover"]["tmp_name"], $new_filename)) {

                // Insert file path into the database - prepared statement
                $stmt = $conn->prepare("UPDATE books SET book_cover = ? WHERE id = ?");     // ? means that data will be inserted later - preventing change of the SQL statement
                $stmt->bind_param("si", $new_filename, $book_id);

                if ($stmt->execute()) {         // Uses prepared statement - SQL Injection will be ignored, because data aren't included into the SQL statement
                    echo "File uploaded successfully.";
                } else {
                    echo "Error: Could not save the file path to the database.";
                }
                $stmt->close();
            } else {
                $errors[] = "Error: There was a problem uploading your file.";
            }
        } else {
            $errors[] = "Error: There was a problem with the file type.";
        }
    } else {
        $errors[] = "Error: " . $_FILES["book_cover"]["error"];
    }

    // Display errors
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo htmlspecialchars($error) . "<br>";
        }
    }
}

$conn->close();
?>

<!-- HTML form -->
<form method="POST" action="insert_image.php" enctype="multipart/form-data">
    Book ID: <input type="number" name="book_id" required><br>
    Select image to upload:
    <input type="file" name="book_cover" required><br>
    <input type="submit" value="Upload Image">
</form>