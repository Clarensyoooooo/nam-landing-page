<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    setAlert('Invalid service ID.', 'danger');
    header('Location: ../admin/dashboard.php?page=services');
    exit();
}

$service = getServiceById($conn, $id);

if (!$service) {
    setAlert('Service not found.', 'danger');
    header('Location: ../admin/dashboard.php?page=services');
    exit();
}

// ── Collect every image path associated with this service ────────────────────
$image_paths = [];

// 1. Images in service_images table (multi-image support)
$img_result = $conn->query("SELECT image_path FROM service_images WHERE service_id = " . $id);
if ($img_result) {
    while ($row = $img_result->fetch_assoc()) {
        if (!empty($row['image_path'])) {
            $image_paths[] = $row['image_path'];
        }
    }
}

// 2. Legacy single image_path on the service row itself
if (!empty($service['image_path']) && !in_array($service['image_path'], $image_paths)) {
    $image_paths[] = $service['image_path'];
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
            error_log('[delete_service] Could not delete file: ' . $full_path);
        }
    }
}

// ── Delete service_images rows (FK cascade handles it too, but be explicit) ──
$conn->query("DELETE FROM service_images WHERE service_id = " . $id);

// ── Delete service row ───────────────────────────────────────────────────────
$stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $msg = 'Service "' . htmlspecialchars($service['service_name']) . '" deleted.';
    if ($deleted_files > 0) $msg .= ' ' . $deleted_files . ' image' . ($deleted_files > 1 ? 's' : '') . ' removed from server.';
    if ($failed_files  > 0) $msg .= ' Warning: ' . $failed_files . ' file(s) could not be deleted from disk.';
    setAlert($msg, 'success');
} else {
    setAlert('Failed to delete service: ' . $stmt->error, 'danger');
}
$stmt->close();

header('Location: ../admin/dashboard.php?page=services');
exit();