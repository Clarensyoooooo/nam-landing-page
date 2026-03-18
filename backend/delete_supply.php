<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    setAlert('Invalid supply ID.', 'danger');
    header('Location: ../admin/dashboard.php?page=supplies');
    exit();
}

$result = $conn->query("SELECT * FROM supplies WHERE id = " . $id);
$supply = $result ? $result->fetch_assoc() : null;

if (!$supply) {
    setAlert('Supply not found.', 'danger');
    header('Location: ../admin/dashboard.php?page=supplies');
    exit();
}

// ── Delete image file from disk ──────────────────────────────────────────────
$deleted_files = 0;
$failed_files  = 0;

if (!empty($supply['image_path'])) {
    $full_path = UPLOADS_PATH . $supply['image_path'];
    if (file_exists($full_path) && is_file($full_path)) {
        if (unlink($full_path)) {
            $deleted_files++;
        } else {
            $failed_files++;
            error_log('[delete_supply] Could not delete file: ' . $full_path);
        }
    }
}

// ── Delete supply row ────────────────────────────────────────────────────────
$stmt = $conn->prepare("DELETE FROM supplies WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $msg = 'Supply "' . htmlspecialchars($supply['supply_name']) . '" deleted.';
    if ($deleted_files > 0) $msg .= ' Image removed from server.';
    if ($failed_files  > 0) $msg .= ' Warning: could not delete image file from disk.';
    setAlert($msg, 'success');
} else {
    setAlert('Failed to delete supply: ' . $stmt->error, 'danger');
}
$stmt->close();

header('Location: ../admin/dashboard.php?page=supplies');
exit();