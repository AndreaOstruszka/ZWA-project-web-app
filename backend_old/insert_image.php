<?php
// Database connection
require_once 'db_connection.php';

// Initialize error message variables
$errors = [];

// Function to resize image
function resize_image($file, $ext, $max_width, $max_height)
{
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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = intval($_POST["book_id"]);

    // Retrieve the book title from the database
    $stmt = $conn->prepare("SELECT title FROM books WHERE id = :book_id");
    $stmt->bindValue(":book_id", $book_id, PDO::PARAM_INT);
    $stmt->execute();
    $book_title = $stmt->fetchColumn();

    if (empty($book_title)) {
        $errors[] = "Error: Book not found.";
    } else {
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
                if (!is_dir("./uploads")) {
                    if (!mkdir("./uploads", 0777, true)) {
                        $errors[] = "Error: Failed to create uploads directory.";
                    }
                }

                // Rename the file based on the book title
                $sanitized_title = preg_replace('/[^a-zA-Z0-9_-]/', '_', $book_title);
                $new_filename = $sanitized_title . '.' . $ext;

                // Define file paths
                $large_filename = "./uploads/" . $new_filename;
                $small_filename = "./uploads/small_" . $new_filename;

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

                    // Insert file paths into the database
                    $stmt = $conn->prepare("UPDATE books SET book_cover_large = :large_filename, book_cover_small = :small_filename WHERE id = :book_id");
                    $stmt->bindValue(":large_filename", $large_filename, PDO::PARAM_STR);
                    $stmt->bindValue(":small_filename", $small_filename, PDO::PARAM_STR);
                    $stmt->bindValue(":book_id", $book_id, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        echo "File uploaded and resized successfully.";
                    } else {
                        echo "Error: Could not save the file paths to the database.";
                    }
                } else {
                    $errors[] = "Error: There was a problem uploading your file.";
                }
            }
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