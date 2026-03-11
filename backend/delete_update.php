<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    setAlert('Invalid post ID', 'danger');
    header('Location: ../admin/dashboard.php?page=updates');
    exit();
}

$result = $conn->query("SELECT image_path FROM updates WHERE id = $id");
$row    = $result ? $result->fetch_assoc() : null;

if (!$row) {
    setAlert('Post not found', 'danger');
    header('Location: ../admin/dashboard.php?page=updates');
    exit();
}

if ($row['image_path']) deleteFile(UPLOADS_PATH . $row['image_path']);

$stmt = $conn->prepare("DELETE FROM updates WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    setAlert('Post deleted successfully', 'success');
} else {
    setAlert('Failed to delete post: ' . $stmt->error, 'danger');
}
$stmt->close();

header('Location: ../admin/dashboard.php?page=updates');
exit();
