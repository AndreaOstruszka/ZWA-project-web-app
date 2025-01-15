<?php
/**
 * Sanitizes a book title for use in file paths.
 *
 * This function replaces spaces and special characters in the title with underscores
 * to create a sanitized version suitable for file paths.
 *
 * @param string $title The book title to sanitize.
 * @return string The sanitized title.
 */
function sanitizeTitleForPath($title) {
    // Replace spaces and special characters with underscores
    return preg_replace('/[^a-zA-Z0-9_\-]/', '_', $title);
}

/**
 * Checks if a small cover image exists for a given book title.
 *
 * This function checks for the existence of a small cover image file for the given book title.
 * If no specific cover image is found, a default placeholder image is returned.
 *
 * @param string $title The book title to check for a cover image.
 * @return string The file path of the small cover image or the default placeholder.
 */
function getCoverImageSmall($title) {
    $ext = ['jpg', 'jpeg', 'png', 'gif']; // Allowed file extensions
    $coverImage = 'uploads/cover_placeholder_small.jpg'; // Default placeholder
    $sanitizedTitle = sanitizeTitleForPath($title); // Sanitize title
    foreach ($ext as $e) {
        $potentialImage = "uploads/small_{$sanitizedTitle}.{$e}";
        if (file_exists($potentialImage)) {
            $coverImage = $potentialImage;
            break;
        }
    }
    return $coverImage;
}

/**
 * Checks if a large cover image exists for a given book title.
 *
 * This function checks for the existence of a large cover image file for the given book title.
 * If no specific cover image is found, a default placeholder image is returned.
 *
 * @param string $title The book title to check for a cover image.
 * @return string The file path of the large cover image or the default placeholder.
 */
function getCoverImageBig($title) {
    $ext = ['jpg', 'jpeg', 'png', 'gif']; // Allowed file extensions
    $coverImage = 'uploads/cover_placeholder.jpg'; // Default placeholder
    $sanitizedTitle = sanitizeTitleForPath($title); // Sanitize title
    foreach ($ext as $e) {
        $potentialImage = "uploads/{$sanitizedTitle}.{$e}";
        if (file_exists($potentialImage)) {
            $coverImage = $potentialImage;
            break;
        }
    }
    return $coverImage;
}
?>