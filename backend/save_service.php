<?php
ob_start();
header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

$response = ['success' => false, 'message' => ''];

try {
    $service_id   = isset($_POST['service_id']) && !empty($_POST['service_id']) ? intval($_POST['service_id']) : null;
    $service_name = sanitize($_POST['service_name'] ?? '');
    $description  = sanitize($_POST['description']  ?? '');
    $sort_order   = intval($_POST['sort_order']      ?? 0);
    $is_active    = isset($_POST['is_active'])       ? 1 : 0;

    if (empty($service_name)) throw new Exception('Service name is required.');
    if (empty($description))  throw new Exception('Service description is required.');

    // Collect uploaded images
    $uploaded_images = [];
    if (isset($_FILES['service_images']) && is_array($_FILES['service_images']['name'])) {
        $file_count = count($_FILES['service_images']['name']);
        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['service_images']['size'][$i] > 0 && $_FILES['service_images']['error'][$i] === UPLOAD_ERR_OK) {
                $file = [
                    'name'     => $_FILES['service_images']['name'][$i],
                    'type'     => $_FILES['service_images']['type'][$i],
                    'tmp_name' => $_FILES['service_images']['tmp_name'][$i],
                    'error'    => $_FILES['service_images']['error'][$i],
                    'size'     => $_FILES['service_images']['size'][$i],
                ];
                $result = uploadFile($file, UPLOADS_PATH . 'services/');
                if (!$result['success']) throw new Exception($result['error']);
                $uploaded_images[] = 'services/' . $result['filename'];
            }
        }
    }

    if ($service_id) {
        $stmt = $conn->prepare("UPDATE services SET service_name=?, description=?, sort_order=?, is_active=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("ssiii", $service_name, $description, $sort_order, $is_active, $service_id);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $stmt->close();

        if (!empty($uploaded_images)) {
            $max_res = $conn->query("SELECT COALESCE(MAX(sort_order),-1)+1 AS nxt FROM service_images WHERE service_id=$service_id");
            $nxt     = $max_res ? (int)$max_res->fetch_assoc()['nxt'] : 0;
            $ins     = $conn->prepare("INSERT INTO service_images (service_id, image_path, sort_order) VALUES (?,?,?)");
            foreach ($uploaded_images as $p) { $ins->bind_param("isi", $service_id, $p, $nxt); $ins->execute(); $nxt++; }
            $ins->close();
        }

        $img_note = !empty($uploaded_images) ? ' ' . count($uploaded_images) . ' new image(s) added.' : '';
        setAlert('Service "' . $service_name . '" updated successfully.' . $img_note, 'success');
    } else {
        $stmt = $conn->prepare("INSERT INTO services (service_name, description, sort_order, is_active) VALUES (?,?,?,?)");
        $stmt->bind_param("ssii", $service_name, $description, $sort_order, $is_active);
        if (!$stmt->execute()) throw new Exception($stmt->error);
        $new_id = $conn->insert_id;
        $stmt->close();

        if (!empty($uploaded_images)) {
            $ins = $conn->prepare("INSERT INTO service_images (service_id, image_path, sort_order) VALUES (?,?,?)");
            foreach ($uploaded_images as $i => $p) { $ins->bind_param("isi", $new_id, $p, $i); $ins->execute(); }
            $ins->close();
        }

        $img_note = !empty($uploaded_images) ? ' ' . count($uploaded_images) . ' image(s) uploaded.' : '';
        setAlert('Service "' . $service_name . '" added successfully.' . $img_note, 'success');
    }

    $response['success'] = true;
    $response['message'] = $_SESSION['alert']['message'] ?? '';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

ob_end_clean();
echo json_encode($response);