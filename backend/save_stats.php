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
    $ids      = $_POST['ids']      ?? [];
    $labels   = $_POST['labels']   ?? [];
    $values   = $_POST['values']   ?? [];
    $suffixes = $_POST['suffixes'] ?? [];
    $orders   = $_POST['orders']   ?? [];
    $actives  = $_POST['actives']  ?? [];   // keyed by stat id

    if (empty($ids)) {
        throw new Exception('No stats data received.');
    }

    $stmt = $conn->prepare(
        "UPDATE site_stats
         SET label = ?, value = ?, suffix = ?, sort_order = ?, is_active = ?
         WHERE id = ?"
    );

    foreach ($ids as $i => $id) {
        $id        = intval($id);
        $label     = sanitize($labels[$i] ?? '');
        $value     = intval($values[$i]   ?? 0);
        $suffix    = strip_tags(trim($suffixes[$i] ?? ''));   // keep +, k+, etc. intact
        $order     = intval($orders[$i]   ?? $i);
        $is_active = isset($actives[$id]) ? 1 : 0;

        if ($label === '') {
            continue;   // skip blank rows silently
        }

        $stmt->bind_param('sissii', $label, $value, $suffix, $order, $is_active, $id);
        $stmt->execute();
    }

    $stmt->close();

    $response['success'] = true;
    $response['message'] = 'Stats saved successfully.';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

ob_end_clean();
echo json_encode($response);