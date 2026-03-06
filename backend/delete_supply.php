<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    setAlert('Invalid supply ID', 'danger');
    header('Location: ../admin/dashboard.php?page=supplies');
    exit();
}

$result = $conn->query("SELECT image_path FROM supplies WHERE id = $id");
$row    = $result ? $result->fetch_assoc() : null;

if (!$row) {
    setAlert('Supply not found', 'danger');
    header('Location: ../admin/dashboard.php?page=supplies');
    exit();
}

if ($row['image_path']) deleteFile(UPLOADS_PATH . $row['image_path']);

$stmt = $conn->prepare("DELETE FROM supplies WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    setAlert('Supply deleted successfully', 'success');
} else {
    setAlert('Failed to delete supply: ' . $stmt->error, 'danger');
}
$stmt->close();

header('Location: ../admin/dashboard.php?page=supplies');
exit();