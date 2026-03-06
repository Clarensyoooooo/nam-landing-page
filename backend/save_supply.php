<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

$response = ['success' => false, 'message' => ''];

try {
    $supply_id   = isset($_POST['supply_id']) && $_POST['supply_id'] !== '' ? intval($_POST['supply_id']) : null;
    $category_id = intval($_POST['category_id'] ?? 0);
    $supply_name = sanitize($_POST['supply_name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $unit        = sanitize($_POST['unit'] ?? '');
    $sort_order  = intval($_POST['sort_order'] ?? 0);
    $is_active   = isset($_POST['is_active']) ? 1 : 0;
    $image_path  = null;

    if (empty($supply_name))  throw new Exception('Supply name is required.');
    if ($category_id <= 0)    throw new Exception('Please select a category.');

    // Handle image upload
    if (isset($_FILES['supply_image']) && $_FILES['supply_image']['size'] > 0) {
        $upload_result = uploadFile($_FILES['supply_image'], UPLOADS_PATH . 'supplies/');
        if (!$upload_result['success']) throw new Exception($upload_result['error']);
        $image_path = 'supplies/' . $upload_result['filename'];
    }

    // Ensure uploads/supplies/ directory exists
    if (!is_dir(UPLOADS_PATH . 'supplies/')) {
        mkdir(UPLOADS_PATH . 'supplies/', 0755, true);
    }

    if ($supply_id) {
        // UPDATE
        $old = $conn->query("SELECT image_path FROM supplies WHERE id = " . $supply_id)->fetch_assoc();
        if ($image_path && $old && $old['image_path']) {
            deleteFile(UPLOADS_PATH . $old['image_path']);
        }
        if (!$image_path && $old) $image_path = $old['image_path'];

        $stmt = $conn->prepare("UPDATE supplies SET category_id=?, supply_name=?, description=?, unit=?, image_path=?, sort_order=?, is_active=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("issssiiii", $category_id, $supply_name, $description, $unit, $image_path, $sort_order, $is_active, $supply_id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Supply updated successfully.';
        } else {
            throw new Exception($stmt->error);
        }
        $stmt->close();
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO supplies (category_id, supply_name, description, unit, image_path, sort_order, is_active) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("issssii", $category_id, $supply_name, $description, $unit, $image_path, $sort_order, $is_active);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Supply added successfully.';
        } else {
            throw new Exception($stmt->error);
        }
        $stmt->close();
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);