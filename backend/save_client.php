<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

$response = ['success' => false, 'message' => ''];

try {
    $client_id   = isset($_POST['client_id']) && !empty($_POST['client_id']) ? intval($_POST['client_id']) : null;
    $client_name = sanitize($_POST['client_name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $sort_order  = intval($_POST['sort_order'] ?? 0);
    $is_active   = isset($_POST['is_active']) ? 1 : 0;
    $image_path  = null;

    if (empty($client_name)) throw new Exception('Client name is required.');

    // Handle image upload
    if (isset($_FILES['client_image']) && $_FILES['client_image']['size'] > 0) {
        $upload_result = uploadFile($_FILES['client_image'], UPLOADS_PATH . 'clients/');
        if (!$upload_result['success']) throw new Exception($upload_result['error']);
        $image_path = 'clients/' . $upload_result['filename'];
    }

    if ($client_id) {
        $old_client = getClientById($conn, $client_id);
        if ($image_path && !empty($old_client['image_path'])) {
            $old_full = UPLOADS_PATH . $old_client['image_path'];
            if (file_exists($old_full)) unlink($old_full);
        }
        if (!$image_path) $image_path = $old_client['image_path'];

        $stmt = $conn->prepare("UPDATE clients SET client_name=?, description=?, image_path=?, sort_order=?, is_active=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("sssiii", $client_name, $description, $image_path, $sort_order, $is_active, $client_id);
        if ($stmt->execute()) {
            setAlert('Client "' . $client_name . '" updated successfully.', 'success');
        } else {
            throw new Exception($stmt->error);
        }
        $stmt->close();
    } else {
        if (!$image_path) throw new Exception('Client image is required.');
        $stmt = $conn->prepare("INSERT INTO clients (client_name, description, image_path, sort_order, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssii", $client_name, $description, $image_path, $sort_order, $is_active);
        if ($stmt->execute()) {
            setAlert('Client "' . $client_name . '" added successfully.', 'success');
        } else {
            throw new Exception($stmt->error);
        }
        $stmt->close();
    }

    $response['success'] = true;
    $response['message'] = $_SESSION['alert']['message'] ?? '';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);