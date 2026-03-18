<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    jsonResponse(false, 'Invalid image ID.');
}

// Fetch image record
$result = $conn->query("SELECT * FROM service_images WHERE id = " . $id);
$image  = $result ? $result->fetch_assoc() : null;

if (!$image) {
    jsonResponse(false, 'Image not found.');
}

// ── Delete file from disk ────────────────────────────────────────────────────
$file_deleted = false;
$file_warning = '';

if (!empty($image['image_path'])) {
    $full_path = UPLOADS_PATH . $image['image_path'];
    if (file_exists($full_path) && is_file($full_path)) {
        if (unlink($full_path)) {
            $file_deleted = true;
        } else {
            $file_warning = ' (Warning: could not remove file from disk.)';
            error_log('[delete_service_img] Could not delete: ' . $full_path);
        }
    } else {
        // File already missing — treat as deleted
        $file_deleted = true;
    }
}

// ── Delete DB row ────────────────────────────────────────────────────────────
$stmt = $conn->prepare("DELETE FROM service_images WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    jsonResponse(true, 'Image deleted.' . $file_warning);
} else {
    jsonResponse(false, 'Database error: ' . $stmt->error);
}
$stmt->close();