<?php
ob_start();

require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Session expired. Please log in again.']);
    exit();
}

$response = ['success' => false, 'message' => ''];

try {
    $supply_id   = (isset($_POST['supply_id']) && $_POST['supply_id'] !== '') ? intval($_POST['supply_id']) : null;
    $category_id = intval($_POST['category_id'] ?? 0);
    $supply_name = trim($_POST['supply_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $sort_order  = intval($_POST['sort_order'] ?? 0);
    $is_active   = isset($_POST['is_active']) ? 1 : 0;

    if ($supply_name === '') {
        throw new Exception('Supply name is required.');
    }
    if ($category_id <= 0) {
        throw new Exception('Please select a category.');
    }

    // Ensure uploads/supplies/ directory exists
    $supplies_dir = UPLOADS_PATH . 'supplies/';
    if (!is_dir($supplies_dir) && !mkdir($supplies_dir, 0755, true)) {
        throw new Exception('Could not create uploads/supplies/ directory.');
    }

    // Handle optional image upload
    $image_path = null;
    $file = $_FILES['supply_image'] ?? null;
    if ($file && $file['error'] === UPLOAD_ERR_OK && $file['size'] > 0) {
        $upload = uploadFile($file, $supplies_dir);
        if (!$upload['success']) {
            throw new Exception($upload['error']);
        }
        $image_path = 'supplies/' . $upload['filename'];
    }

    if ($supply_id) {
        // Fetch existing so we can keep or replace the image
        $row = $conn->query("SELECT image_path FROM supplies WHERE id = {$supply_id}")->fetch_assoc();
        if (!$row) {
            throw new Exception('Supply not found.');
        }

        // Delete old image when a new one is uploaded
        if ($image_path && !empty($row['image_path'])) {
            $old_full = UPLOADS_PATH . $row['image_path'];
            if (file_exists($old_full)) {
                @unlink($old_full);
            }
        }

        // Keep old image when none supplied
        if (!$image_path) {
            $image_path = $row['image_path'];
        }

        $stmt = $conn->prepare(
            "UPDATE supplies
             SET category_id = ?, supply_name = ?, description = ?, image_path = ?,
                 sort_order = ?, is_active = ?, updated_at = NOW()
             WHERE id = ?"
        );
        $stmt->bind_param('issssii', $category_id, $supply_name, $description, $image_path, $sort_order, $is_active, $supply_id);
        $stmt->execute();
        $stmt->close();

        $response['success'] = true;
        $response['message'] = "Supply \"{$supply_name}\" updated successfully.";

    } else {
        $stmt = $conn->prepare(
            "INSERT INTO supplies (category_id, supply_name, description, image_path, sort_order, is_active)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('isssii', $category_id, $supply_name, $description, $image_path, $sort_order, $is_active);
        $stmt->execute();
        $stmt->close();

        $response['success'] = true;
        $response['message'] = "Supply \"{$supply_name}\" added successfully.";
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

ob_end_clean();
echo json_encode($response);