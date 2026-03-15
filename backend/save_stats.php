<?php
ob_start();
header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

$response = ['success' => false, 'message' => ''];

try {
    $ids      = $_POST['ids']      ?? [];
    $labels   = $_POST['labels']   ?? [];
    $values   = $_POST['values']   ?? [];
    $suffixes = $_POST['suffixes'] ?? [];
    $orders   = $_POST['orders']   ?? [];
    $actives  = $_POST['actives']  ?? [];

    if (empty($ids)) throw new Exception('No stats data received.');

    $stmt = $conn->prepare("
        UPDATE site_stats
        SET label = ?, value = ?, suffix = ?, sort_order = ?, is_active = ?
        WHERE id = ?
    ");

    foreach ($ids as $i => $id) {
        $id        = intval($id);
        $label     = sanitize($labels[$i] ?? '');
        $value     = intval($values[$i]   ?? 0);

        // Use strip_tags only — NOT sanitize() which converts + to &#43; etc.
        $suffix    = strip_tags(trim($suffixes[$i] ?? ''));

        $order     = intval($orders[$i]   ?? $i);
        $is_active = isset($actives[$id]) ? 1 : 0;

        if (empty($label)) continue;

        $stmt->bind_param('sissii', $label, $value, $suffix, $order, $is_active, $id);
        if (!$stmt->execute()) throw new Exception($stmt->error);
    }

    $stmt->close();
    $response['success'] = true;
    $response['message'] = 'Stats saved.';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

ob_end_clean();
echo json_encode($response);