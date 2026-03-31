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
    // Validate & collect inputs
    $category_id   = (isset($_POST['category_id']) && $_POST['category_id'] !== '') ? intval($_POST['category_id']) : null;
    $category_name = trim($_POST['category_name'] ?? '');
    $description   = trim($_POST['description']   ?? '');
    $sort_order    = intval($_POST['sort_order']   ?? 0);
    $is_active     = isset($_POST['is_active'])    ? 1 : 0;

    if ($category_name === '') {
        throw new Exception('Category name is required.');
    }

    if ($category_id) {
        // UPDATE
        $stmt = $conn->prepare(
            "UPDATE supply_categories
             SET category_name = ?, description = ?, sort_order = ?, is_active = ?, updated_at = NOW()
             WHERE id = ?"
        );
        $stmt->bind_param('ssiii', $category_name, $description, $sort_order, $is_active, $category_id);
        $stmt->execute();
        $stmt->close();

        $response['success'] = true;
        $response['message'] = "Category \"{$category_name}\" updated successfully.";
    } else {
        // INSERT
        $stmt = $conn->prepare(
            "INSERT INTO supply_categories (category_name, description, sort_order, is_active)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param('ssii', $category_name, $description, $sort_order, $is_active);
        $stmt->execute();
        $stmt->close();

        $response['success'] = true;
        $response['message'] = "Category \"{$category_name}\" added successfully.";
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

ob_end_clean();
echo json_encode($response);