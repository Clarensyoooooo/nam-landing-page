<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    setAlert('Invalid category ID', 'danger');
    header('Location: ../admin/dashboard.php?page=supplies');
    exit();
}

// Delete all supply images in this category first
$img_result = $conn->query("SELECT image_path FROM supplies WHERE category_id = $id AND image_path IS NOT NULL");
if ($img_result) {
    while ($row = $img_result->fetch_assoc()) {
        if ($row['image_path']) deleteFile(UPLOADS_PATH . $row['image_path']);
    }
}

// Delete supplies (FK cascade will handle it, but be explicit)
$conn->query("DELETE FROM supplies WHERE category_id = $id");

// Delete the category
$stmt = $conn->prepare("DELETE FROM supply_categories WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    setAlert('Category and all its supplies deleted successfully', 'success');
} else {
    setAlert('Failed to delete category: ' . $stmt->error, 'danger');
}
$stmt->close();

header('Location: ../admin/dashboard.php?page=supplies');
exit();