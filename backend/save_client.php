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
    $client_id   = (isset($_POST['client_id']) && $_POST['client_id'] !== '') ? intval($_POST['client_id']) : null;
    $client_name = trim($_POST['client_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $sort_order  = intval($_POST['sort_order'] ?? 0);
    $is_active   = isset($_POST['is_active']) ? 1 : 0;

    if ($client_name === '') {
        throw new Exception('Client name is required.');
    }

    // Handle optional image upload
    $image_path = null;
    $file = $_FILES['client_image'] ?? null;
    if ($file && $file['error'] === UPLOAD_ERR_OK && $file['size'] > 0) {
        $upload = uploadFile($file, UPLOADS_PATH . 'clients/');
        if (!$upload['success']) {
            throw new Exception($upload['error']);
        }
        $image_path = 'clients/' . $upload['filename'];
    }

    if ($client_id) {
        // Fetch existing record so we keep the old image if none uploaded
        $existing = getClientById($conn, $client_id);
        if (!$existing) {
            throw new Exception('Client not found.');
        }

        // Delete old image from disk only when a new one is supplied
        if ($image_path && !empty($existing['image_path'])) {
            $old_full = UPLOADS_PATH . $existing['image_path'];
            if (file_exists($old_full)) {
                @unlink($old_full);
            }
        }

        // Fall back to existing path when no new image was uploaded
        if (!$image_path) {
            $image_path = $existing['image_path'];
        }

        $stmt = $conn->prepare(
            "UPDATE clients
             SET client_name = ?, description = ?, image_path = ?, sort_order = ?, is_active = ?, updated_at = NOW()
             WHERE id = ?"
        );
        $stmt->bind_param('sssiii', $client_name, $description, $image_path, $sort_order, $is_active, $client_id);
        $stmt->execute();
        $stmt->close();

        $response['success'] = true;
        $response['message'] = "Client \"{$client_name}\" updated successfully.";

    } else {
        // New client — image is required
        if (!$image_path) {
            throw new Exception('Client image is required.');
        }

        $stmt = $conn->prepare(
            "INSERT INTO clients (client_name, description, image_path, sort_order, is_active)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('sssii', $client_name, $description, $image_path, $sort_order, $is_active);
        $stmt->execute();
        $stmt->close();

        $response['success'] = true;
        $response['message'] = "Client \"{$client_name}\" added successfully.";
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

ob_end_clean();
echo json_encode($response);