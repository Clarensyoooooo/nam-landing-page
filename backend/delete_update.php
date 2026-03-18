<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    setAlert('Invalid post ID.', 'danger');
    header('Location: ../admin/dashboard.php?page=updates');
    exit();
}

$result = $conn->query("SELECT * FROM updates WHERE id = " . $id);
$update = $result ? $result->fetch_assoc() : null;

if (!$update) {
    setAlert('Post not found.', 'danger');
    header('Location: ../admin/dashboard.php?page=updates');
    exit();
}

// ── Collect every image path for this post ───────────────────────────────────
$image_paths = [];

// 1. All images in update_images table
$imgs_result = $conn->query("SELECT image_path FROM update_images WHERE update_id = " . $id);
if ($imgs_result) {
    while ($row = $imgs_result->fetch_assoc()) {
        if (!empty($row['image_path'])) {
            $image_paths[] = $row['image_path'];
        }
    }
}

// 2. Cover / legacy image_path on the update row
if (!empty($update['image_path']) && !in_array($update['image_path'], $image_paths)) {
    $image_paths[] = $update['image_path'];
}

// ── Delete all image files from disk ─────────────────────────────────────────
$deleted_files = 0;
$failed_files  = 0;

foreach ($image_paths as $path) {
    $full_path = UPLOADS_PATH . $path;
    if (file_exists($full_path) && is_file($full_path)) {
        if (unlink($full_path)) {
            $deleted_files++;
        } else {
            $failed_files++;
            error_log('[delete_update] Could not delete file: ' . $full_path);
        }
    }
}

// ── Delete update_images rows (FK cascade handles it, but be explicit) ───────
$conn->query("DELETE FROM update_images WHERE update_id = " . $id);

// ── Delete update row ────────────────────────────────────────────────────────
$stmt = $conn->prepare("DELETE FROM updates WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $msg = 'Post "' . htmlspecialchars($update['title']) . '" deleted.';
    if ($deleted_files > 0) $msg .= ' ' . $deleted_files . ' image' . ($deleted_files > 1 ? 's' : '') . ' removed from server.';
    if ($failed_files  > 0) $msg .= ' Warning: ' . $failed_files . ' file(s) could not be deleted from disk.';
    setAlert($msg, 'success');
} else {
    setAlert('Failed to delete post: ' . $stmt->error, 'danger');
}
$stmt->close();

header('Location: ../admin/dashboard.php?page=updates');
exit();