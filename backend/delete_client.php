<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    setAlert('Invalid client ID.', 'danger');
    header('Location: ../admin/dashboard.php?page=clients');
    exit();
}

$client = getClientById($conn, $id);

if (!$client) {
    setAlert('Client not found.', 'danger');
    header('Location: ../admin/dashboard.php?page=clients');
    exit();
}

// ── Delete image file from disk ──────────────────────────────────────────────
$deleted_files  = 0;
$failed_files   = 0;

if (!empty($client['image_path'])) {
    $full_path = UPLOADS_PATH . $client['image_path'];
    if (file_exists($full_path) && is_file($full_path)) {
        if (unlink($full_path)) {
            $deleted_files++;
        } else {
            $failed_files++;
            error_log('[delete_client] Could not delete file: ' . $full_path);
        }
    }
}

// ── Delete from database ─────────────────────────────────────────────────────
$stmt = $conn->prepare("DELETE FROM clients WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $msg = 'Client "' . htmlspecialchars($client['client_name']) . '" deleted.';
    if ($deleted_files > 0) $msg .= ' Image removed from server.';
    if ($failed_files  > 0) $msg .= ' Warning: could not delete image file from disk.';
    setAlert($msg, 'success');
} else {
    setAlert('Failed to delete client: ' . $stmt->error, 'danger');
}
$stmt->close();

header('Location: ../admin/dashboard.php?page=clients');
exit();