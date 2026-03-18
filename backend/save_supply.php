<?php
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
    $sort_order  = intval($_POST['sort_order']    ?? 0);
    $is_active   = isset($_POST['is_active'])     ? 1 : 0;
    $image_path  = null;

    if (empty($supply_name)) throw new Exception('Supply name is required.');
    if ($category_id <= 0)   throw new Exception('Please select a category.');

    $supplies_dir = UPLOADS_PATH . 'supplies/';
    if (!is_dir($supplies_dir)) {
        if (!mkdir($supplies_dir, 0755, true)) throw new Exception('Could not create uploads/supplies/ directory.');
    }

    if (isset($_FILES['supply_image']) && $_FILES['supply_image']['error'] === UPLOAD_ERR_OK && $_FILES['supply_image']['size'] > 0) {
        $result = uploadFile($_FILES['supply_image'], $supplies_dir);
        if (!$result['success']) throw new Exception($result['error']);
        $image_path = 'supplies/' . $result['filename'];
    }

    if ($supply_id) {
        $old = $conn->query("SELECT image_path FROM supplies WHERE id=" . intval($supply_id))->fetch_assoc();
        if ($image_path && $old && !empty($old['image_path'])) {
            $old_full = UPLOADS_PATH . $old['image_path'];
            if (file_exists($old_full)) unlink($old_full);
        }
        if (!$image_path && $old) $image_path = $old['image_path'];

        $stmt = $conn->prepare("UPDATE supplies SET category_id=?, supply_name=?, description=?, image_path=?, sort_order=?, is_active=?, updated_at=NOW() WHERE id=?");
        if (!$stmt) throw new Exception('Prepare failed: ' . $conn->error);
        $stmt->bind_param("issssii", $category_id, $supply_name, $description, $image_path, $sort_order, $is_active, $supply_id);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();
        setAlert('Supply "' . $supply_name . '" updated successfully.', 'success');
    } else {
        $stmt = $conn->prepare("INSERT INTO supplies (category_id, supply_name, description, image_path, sort_order, is_active) VALUES (?,?,?,?,?,?)");
        if (!$stmt) throw new Exception('Prepare failed: ' . $conn->error);
        $stmt->bind_param("isssii", $category_id, $supply_name, $description, $image_path, $sort_order, $is_active);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();
        setAlert('Supply "' . $supply_name . '" added successfully.', 'success');
    }

    $response['success'] = true;
    $response['message'] = $_SESSION['alert']['message'] ?? '';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

ob_end_clean();
echo json_encode($response);    