<?php
// Function to sanitize title for file paths
function sanitizeTitleForPath($title) {
    // Replace spaces and special characters with underscores
    return preg_replace('/[^a-zA-Z0-9_\-]/', '_', $title);
}

// Function to check if small cover image exists
function getCoverImageSmall($title) {
    $ext = ['jpg', 'jpeg', 'png', 'gif'];
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

// Function to check if big cover image exists
function getCoverImageBig($title) {
    $ext = ['jpg', 'jpeg', 'png', 'gif'];
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
