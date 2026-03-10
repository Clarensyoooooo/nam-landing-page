<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

$response = ['success' => false, 'message' => ''];

try {
    $category_id   = isset($_POST['category_id']) && $_POST['category_id'] !== '' ? intval($_POST['category_id']) : null;
    $category_name = sanitize($_POST['category_name'] ?? '');
    $description   = sanitize($_POST['description']   ?? '');
    $sort_order    = intval($_POST['sort_order'] ?? 0);
    $is_active     = isset($_POST['is_active']) ? 1 : 0;

    if (empty($category_name)) throw new Exception('Category name is required.');

    if ($category_id) {
        $stmt = $conn->prepare("UPDATE supply_categories SET category_name=?, description=?, sort_order=?, is_active=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("ssiii", $category_name, $description, $sort_order, $is_active, $category_id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Category updated successfully.';
        } else {
            throw new Exception($stmt->error);
        }
        $stmt->close();
    } else {
        $stmt = $conn->prepare("INSERT INTO supply_categories (category_name, description, sort_order, is_active) VALUES (?,?,?,?)");
        $stmt->bind_param("ssii", $category_name, $description, $sort_order, $is_active);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Category added successfully.';
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