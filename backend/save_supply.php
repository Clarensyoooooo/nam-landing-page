<?php
// Output buffering prevents any stray PHP warnings from corrupting the JSON response
ob_start();

header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

$response = ['success' => false, 'message' => ''];

try {
    $supply_id   = isset($_POST['supply_id']) && $_POST['supply_id'] !== '' ? intval($_POST['supply_id']) : null;
    $category_id = intval($_POST['category_id'] ?? 0);
    $supply_name = sanitize($_POST['supply_name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $sort_order  = intval($_POST['sort_order'] ?? 0);
    $is_active   = isset($_POST['is_active']) ? 1 : 0;
    $image_path  = null;

    if (empty($supply_name))  throw new Exception('Supply name is required.');
    if ($category_id <= 0)    throw new Exception('Please select a category.');

    // Ensure uploads/supplies/ directory exists BEFORE attempting upload
    $supplies_dir = UPLOADS_PATH . 'supplies/';
    if (!is_dir($supplies_dir)) {
        if (!mkdir($supplies_dir, 0755, true)) {
            throw new Exception('Could not create uploads/supplies/ directory. Check server permissions.');
        }
    }

    // Handle image upload
    if (isset($_FILES['supply_image']) && $_FILES['supply_image']['error'] === UPLOAD_ERR_OK && $_FILES['supply_image']['size'] > 0) {
        $upload_result = uploadFile($_FILES['supply_image'], $supplies_dir);
        if (!$upload_result['success']) throw new Exception($upload_result['error']);
        $image_path = 'supplies/' . $upload_result['filename'];
    } elseif (isset($_FILES['supply_image']) && $_FILES['supply_image']['error'] !== UPLOAD_ERR_NO_FILE && $_FILES['supply_image']['error'] !== UPLOAD_ERR_OK) {
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds server upload limit (upload_max_filesize).',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds form upload limit.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder on server.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension blocked the upload.',
        ];
        $err_code = $_FILES['supply_image']['error'];
        throw new Exception($upload_errors[$err_code] ?? 'Unknown file upload error (code ' . $err_code . ').');
    }

    if ($supply_id) {
        // UPDATE
        $old_result = $conn->query("SELECT image_path FROM supplies WHERE id = " . intval($supply_id));
        $old = $old_result ? $old_result->fetch_assoc() : null;

        if ($image_path && $old && !empty($old['image_path'])) {
            deleteFile(UPLOADS_PATH . $old['image_path']);
        }
        if (!$image_path && $old) {
            $image_path = $old['image_path'];
        }

        $stmt = $conn->prepare(
            "UPDATE supplies SET category_id=?, supply_name=?, description=?, image_path=?, sort_order=?, is_active=?, updated_at=NOW() WHERE id=?"
        );
        if (!$stmt) throw new Exception('Prepare failed: ' . $conn->error);
        $stmt->bind_param("issssii", $category_id, $supply_name, $description, $image_path, $sort_order, $is_active, $supply_id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Supply updated successfully.';
        } else {
            throw new Exception('Update failed: ' . $stmt->error);
        }
        $stmt->close();

    } else {
        // INSERT
        $stmt = $conn->prepare(
            "INSERT INTO supplies (category_id, supply_name, description, image_path, sort_order, is_active) VALUES (?,?,?,?,?,?)"
        );
        if (!$stmt) throw new Exception('Prepare failed: ' . $conn->error);
        $stmt->bind_param("isssii", $category_id, $supply_name, $description, $image_path, $sort_order, $is_active);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Supply added successfully.';
        } else {
            throw new Exception('Insert failed: ' . $stmt->error);
        }
        $stmt->close();
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Discard any stray output so only clean JSON is sent
ob_end_clean();
echo json_encode($response);