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

    if (empty($title))       throw new Exception('Post title is required.');
    if (empty($description)) throw new Exception('Description is required.');

    // Ensure uploads/updates directory exists
    $uploads_dir = UPLOADS_PATH . 'updates/';
    if (!is_dir($uploads_dir)) {
        if (!mkdir($uploads_dir, 0755, true)) {
            throw new Exception('Could not create uploads/updates/ directory.');
        }
    }

    // ── Collect all uploaded files (update_images[] multi-file field) ──
    $new_image_paths = [];
    if (!empty($_FILES['update_images']['name'][0])) {
        $file_count = count($_FILES['update_images']['name']);
        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['update_images']['error'][$i] !== UPLOAD_ERR_OK) continue;
            $file_item = [
                'name'     => $_FILES['update_images']['name'][$i],
                'type'     => $_FILES['update_images']['type'][$i],
                'tmp_name' => $_FILES['update_images']['tmp_name'][$i],
                'error'    => $_FILES['update_images']['error'][$i],
                'size'     => $_FILES['update_images']['size'][$i],
            ];
            $result = uploadFile($file_item, $uploads_dir);
            if (!$result['success']) throw new Exception($result['error']);
            $new_image_paths[] = 'updates/' . $result['filename'];
        }
    }

    // ── Determine cover image (image_path) ──
    $cover_path = null;
    if (!empty($new_image_paths)) {
        $cover_path = $new_image_paths[0];
    } elseif ($update_id) {
        $old = $conn->query("SELECT image_path FROM updates WHERE id=" . intval($update_id))->fetch_assoc();
        $cover_path = $old['image_path'] ?? null;
    }

    if ($update_id) {
        // ── UPDATE existing post ──
        $stmt = $conn->prepare("UPDATE updates SET title=?, description=?, image_path=?, sort_order=?, is_active=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("sssiii", $title, $description, $cover_path, $sort_order, $is_active, $update_id);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();

        // Append new images (don't delete old ones unless user ticked "remove")
        if (!empty($new_image_paths)) {
            $max_order_res = $conn->query("SELECT COALESCE(MAX(sort_order),0)+1 AS nxt FROM update_images WHERE update_id=$update_id");
            $nxt = $max_order_res ? (int)$max_order_res->fetch_assoc()['nxt'] : 0;

            $ins = $conn->prepare("INSERT INTO update_images (update_id, image_path, sort_order) VALUES (?,?,?)");
            foreach ($new_image_paths as $pi => $path) {
                $ord = $nxt + $pi;
                $ins->bind_param("isi", $update_id, $path, $ord);
                $ins->execute();
            }
            $ins->close();
        }

    } else {
        // ── INSERT new post ──
        $stmt = $conn->prepare("INSERT INTO updates (title, description, image_path, sort_order, is_active) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssii", $title, $description, $cover_path, $sort_order, $is_active);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $new_id = $conn->insert_id;
        $stmt->close();

        // Insert all images into update_images
        if (!empty($new_image_paths)) {
            $ins = $conn->prepare("INSERT INTO update_images (update_id, image_path, sort_order) VALUES (?,?,?)");
            foreach ($new_image_paths as $pi => $path) {
                $ins->bind_param("isi", $new_id, $path, $pi);
                $ins->execute();
            }
            $ins->close();
        }
    }

    $response['success'] = true;
    $response['message'] = $update_id ? 'Post updated successfully.' : 'Post created successfully.';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

ob_end_clean();
echo json_encode($response);