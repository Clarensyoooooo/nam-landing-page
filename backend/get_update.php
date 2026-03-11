<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) jsonResponse(false, 'Invalid ID');

$result = $conn->query("SELECT * FROM updates WHERE id = $id");
$row    = $result ? $result->fetch_assoc() : null;
if (!$row) jsonResponse(false, 'Post not found');

// Attach all images for the modal slideshow
$imgs_result = $conn->query("SELECT image_path FROM update_images WHERE update_id = $id ORDER BY sort_order ASC");
$extra = $imgs_result ? $imgs_result->fetch_all(MYSQLI_ASSOC) : [];

$all_imgs = [];
if (!empty($row['image_path'])) $all_imgs[] = UPLOADS_URL . $row['image_path'];
foreach ($extra as $ei) {
    $full = UPLOADS_URL . $ei['image_path'];
    if (!in_array($full, $all_imgs)) $all_imgs[] = $full;
}
$row['all_images'] = $all_imgs;

jsonResponse(true, 'Found', $row);