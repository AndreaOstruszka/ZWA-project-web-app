<?php
// Database connection
require_once 'db_connection.php';

// Initialize error message variables
$errors = [];

// Function to resize image
function resize_image($file, $ext, $max_width, $max_height) {
    list($width, $height) = getimagesize($file);
    $ratio = $width / $height;

    if ($max_width / $max_height > $ratio) {
        $new_width = $max_height * $ratio;
        $new_height = $max_height;
    } else {
        $new_height = $max_width / $ratio;
        $new_width = $max_width;
    }

    $src = null;
    if ($ext == "jpg" || $ext == "jpeg") {
        $src = imagecreatefromjpeg($file);
    } elseif ($ext == "png") {
        $src = imagecreatefrompng($file);
    } elseif ($ext == "gif") {
        $src = imagecreatefromgif($file);
    }

    $dst = imagecreatetruecolor($new_width, $new_height);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    return $dst;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {         // This block starts only if the form method is POST
    $book_id = intval($_POST["book_id"]);           // Intval ensures that book_id is an integer

    // Retrieve the book name from the database
    $stmt = $conn->prepare("SELECT name FROM books WHERE id = :book_id");
    $stmt->bindValue(":book_id", $book_id, PDO::PARAM_INT);
    $stmt->execute();
    //$stmt->bindValue(1, $book_id, PDO::PARAM_INT);
    $book_name = $stmt->fetchColumn(0);

    if (empty($book_name)) {
        $errors[] = "Error: Book not found.";
    } else {
        // Check if file was uploaded without errors
        if (isset($_FILES["book_cover"]) && $_FILES["book_cover"]["error"] == 0) {
            var_dump($_FILES["book_cover"]);
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
                if (!is_dir( "./uploads")) {
                    echo "test";
                    //mkdir("uploads", 0777, true);
                }
                // Create filenames using the book name
                $book_name_sanitized = preg_replace('/[^A-Za-z0-9_\-]/', '_', $book_name); // Sanitize book name
                $large_filename = "uploads/" . $book_name_sanitized . "_large." . $ext;
                $small_filename = "uploads/" . $book_name_sanitized . "_small." . $ext;

                // Move the uploaded file to the server
                if (move_uploaded_file($_FILES["book_cover"]["tmp_name"], $large_filename)) {
                    // Resize the image
                    $resized_image = resize_image($large_filename, $ext, 200, 200);

                    if ($ext == "jpg" || $ext == "jpeg") {
                        imagejpeg($resized_image, $small_filename);
                    } elseif ($ext == "png") {
                        imagepng($resized_image, $small_filename);
                    } elseif ($ext == "gif") {
                        imagegif($resized_image, $small_filename);
                    }

                    // Insert file paths into the database - prepared statement
                    $stmt = $conn->prepare("UPDATE books SET book_cover_large = ?, book_cover_small = ? WHERE id = ?");     // ? means that data will be inserted later - preventing change of the SQL statement
                    $stmt->bindValue(1, $large_filename, PDO::PARAM_STR);
                    $stmt->bindValue(2, $small_filename, PDO::PARAM_STR);
                    $stmt->bindValue(3, $book_id, PDO::PARAM_INT);

                    if ($stmt->execute()) {         // Uses prepared statement - SQL Injection will be ignored, because data aren't included into the SQL statement
                        echo "File uploaded and resized successfully.";
                    } else {
                        echo "Error: Could not save the file paths to the database.";
                    }
                } else {
                    $errors[] = "Error: There was a problem uploading your file.";
                }
            } else {
                $errors[] = "Error: There was a problem with the file type.";
            }
        } else {
            $errors[] = "Error: " . $_FILES["book_cover"]["error"];
        }
    }

    // Display errors
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo htmlspecialchars($error) . "<br>";
        }
    }
}
?>

<!-- HTML form -->
<form method="POST" action="insert_image.php" enctype="multipart/form-data">
    Book ID: <input type="number" name="book_id" required><br>
    Select image to upload:
    <input type="file" name="book_cover" required><br>
    <input type="submit" value="Upload Image">
</form>
