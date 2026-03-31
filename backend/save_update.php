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
    $update_id   = (isset($_POST['update_id']) && $_POST['update_id'] !== '') ? intval($_POST['update_id']) : null;
    $title       = trim($_POST['title']       ?? '');
    $description = trim($_POST['description'] ?? '');
    $sort_order  = intval($_POST['sort_order'] ?? 0);
    $is_active   = isset($_POST['is_active'])  ? 1 : 0;

    if ($title === '') {
        throw new Exception('Post title is required.');
    }
    if ($description === '') {
        throw new Exception('Description is required.');
    }

    // Ensure uploads/updates/ directory exists
    $updates_dir = UPLOADS_PATH . 'updates/';
    if (!is_dir($updates_dir) && !mkdir($updates_dir, 0755, true)) {
        throw new Exception('Could not create uploads/updates/ directory.');
    }

    // Collect any uploaded images
    $new_image_paths = [];
    if (!empty($_FILES['update_images']['name'][0])) {
        $count = count($_FILES['update_images']['name']);
        for ($i = 0; $i < $count; $i++) {
            if ($_FILES['update_images']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }
            $file = [
                'name'     => $_FILES['update_images']['name'][$i],
                'type'     => $_FILES['update_images']['type'][$i],
                'tmp_name' => $_FILES['update_images']['tmp_name'][$i],
                'error'    => $_FILES['update_images']['error'][$i],
                'size'     => $_FILES['update_images']['size'][$i],
            ];
            $upload = uploadFile($file, $updates_dir);
            if (!$upload['success']) {
                throw new Exception($upload['error']);
            }
            $new_image_paths[] = 'updates/' . $upload['filename'];
        }
    }

    // Cover image = first new upload, or keep existing on edit
    $cover_path = !empty($new_image_paths) ? $new_image_paths[0] : null;

    if ($update_id) {
        // Keep existing cover when no new images supplied
        if (!$cover_path) {
            $existing = $conn->query("SELECT image_path FROM updates WHERE id = {$update_id}")->fetch_assoc();
            $cover_path = $existing['image_path'] ?? null;
        }

        $stmt = $conn->prepare(
            "UPDATE updates
             SET title = ?, description = ?, image_path = ?, sort_order = ?, is_active = ?, updated_at = NOW()
             WHERE id = ?"
        );
        $stmt->bind_param('sssiii', $title, $description, $cover_path, $sort_order, $is_active, $update_id);
        $stmt->execute();
        $stmt->close();

        // Append new images to update_images table
        if (!empty($new_image_paths)) {
            $next_order_res = $conn->query(
                "SELECT COALESCE(MAX(sort_order), 0) + 1 AS nxt FROM update_images WHERE update_id = {$update_id}"
            );
            $next_order = $next_order_res ? (int) $next_order_res->fetch_assoc()['nxt'] : 0;

            $ins = $conn->prepare("INSERT INTO update_images (update_id, image_path, sort_order) VALUES (?, ?, ?)");
            foreach ($new_image_paths as $pi => $path) {
                $ord = $next_order + $pi;
                $ins->bind_param('isi', $update_id, $path, $ord);
                $ins->execute();
            }
            $ins->close();
        }

        $img_note = !empty($new_image_paths) ? ' ' . count($new_image_paths) . ' new image(s) added.' : '';
        $response['success'] = true;
        $response['message'] = "Post \"{$title}\" updated successfully.{$img_note}";

    } else {
        // INSERT new post
        $stmt = $conn->prepare(
            "INSERT INTO updates (title, description, image_path, sort_order, is_active)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('sssii', $title, $description, $cover_path, $sort_order, $is_active);
        $stmt->execute();
        $new_id = $conn->insert_id;
        $stmt->close();

        if (!empty($new_image_paths)) {
            $ins = $conn->prepare("INSERT INTO update_images (update_id, image_path, sort_order) VALUES (?, ?, ?)");
            foreach ($new_image_paths as $pi => $path) {
                $ins->bind_param('isi', $new_id, $path, $pi);
                $ins->execute();
            }
            $ins->close();
        }

        $img_note = !empty($new_image_paths) ? ' ' . count($new_image_paths) . ' image(s) uploaded.' : '';
        $response['success'] = true;
        $response['message'] = "Post \"{$title}\" created successfully.{$img_note}";
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

ob_end_clean();
echo json_encode($response);