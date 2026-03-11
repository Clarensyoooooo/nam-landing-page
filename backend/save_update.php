<?php
ob_start();
header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

$response = ['success' => false, 'message' => ''];

try {
    $update_id  = isset($_POST['update_id']) && $_POST['update_id'] !== '' ? intval($_POST['update_id']) : null;
    $title       = sanitize($_POST['title']       ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $sort_order  = intval($_POST['sort_order']     ?? 0);
    $is_active   = isset($_POST['is_active'])      ? 1 : 0;
    $image_path  = null;

    if (empty($title))       throw new Exception('Post title is required.');
    if (empty($description)) throw new Exception('Description is required.');

    // Ensure uploads/updates directory exists
    $uploads_dir = UPLOADS_PATH . 'updates/';
    if (!is_dir($uploads_dir)) {
        if (!mkdir($uploads_dir, 0755, true)) {
            throw new Exception('Could not create uploads/updates/ directory.');
        }
    }

    // Handle image upload
    if (isset($_FILES['update_image']) && $_FILES['update_image']['error'] === UPLOAD_ERR_OK && $_FILES['update_image']['size'] > 0) {
        $upload_result = uploadFile($_FILES['update_image'], $uploads_dir);
        if (!$upload_result['success']) throw new Exception($upload_result['error']);
        $image_path = 'updates/' . $upload_result['filename'];
    }

    if ($update_id) {
        // UPDATE
        $old = $conn->query("SELECT image_path FROM updates WHERE id = " . intval($update_id))->fetch_assoc();

        if ($image_path && $old && !empty($old['image_path'])) {
            deleteFile(UPLOADS_PATH . $old['image_path']);
        }
        if (!$image_path && $old) {
            $image_path = $old['image_path'];
        }

        $stmt = $conn->prepare("UPDATE updates SET title=?, description=?, image_path=?, sort_order=?, is_active=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("sssiii", $title, $description, $image_path, $sort_order, $is_active, $update_id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Post updated successfully.';
        } else {
            throw new Exception($stmt->error);
        }
        $stmt->close();

    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO updates (title, description, image_path, sort_order, is_active) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssii", $title, $description, $image_path, $sort_order, $is_active);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Post created successfully.';
        } else {
            throw new Exception($stmt->error);
        }
        $stmt->close();
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

ob_end_clean();
echo json_encode($response);
