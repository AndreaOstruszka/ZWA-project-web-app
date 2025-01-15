<?php
// Database connection
require_once __DIR__ . '/../src/db_connection.php';

/**
 * Resizes an image to the specified dimensions.
 *
 * @param string $file The path to the image file.
 * @param string $ext The file extension (jpg, jpeg, png, gif).
 * @param int $max_width The maximum width of the resized image.
 * @param int $max_height The maximum height of the resized image.
 * @return resource The resized image resource.
 */
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

/**
 * Handles the image upload process, including validation, resizing, and database update.
 *
 * @param int $book_id The ID of the book to which the image belongs.
 * @param string $book_title The title of the book.
 * @param array $file The uploaded file information from the $_FILES superglobal.
 * @return array|bool Returns true on success, or an array with 'success' => false and 'errors' on failure.
 */
function handle_image_upload($book_id, $book_title, $file)
{
    global $conn;
    $errors = [];

    if (isset($file) && $file["error"] == 0) {
        $allowed = ["jpg" => "image/jpeg", "png" => "image/png", "gif" => "image/gif"];
        $filename = $file["name"];
        $filetype = $file["type"];
        $filesize = $file["size"];

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
                if (!mkdir("../uploads", 0775, true)) {
                    $errors[] = "Error: Failed to create uploads directory. Check directory permissions.";
                } else {
                    // Set the correct permissions if the directory was created
                    chmod("./uploads", 0775);
                }
            }

            // Rename the file based on the book title
            $sanitized_title = preg_replace('/[^a-zA-Z0-9_-]/', '_', $book_title);
            $new_filename = $sanitized_title . '.' . $ext;

            // Define file paths
            $large_filename = "./uploads/" . $new_filename;
            $small_filename = "./uploads/small_" . $new_filename;

            // Move the uploaded file to the server
            if (move_uploaded_file($file["tmp_name"], $large_filename)) {
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
                    return true;
                } else {
                    $errors[] = "Error: Could not save the file paths to the database.";
                }
            } else {
                $errors[] = "Error: There was a problem uploading your file.";
            }
        }
    }

    return ["success" => false, "errors" => $errors];
}
?>