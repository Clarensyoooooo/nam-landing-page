<?php
// Utility Functions

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Escape for database
function dbEscape($conn, $data) {
    return $conn->real_escape_string($data);
}

// Get file extension
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

// Generate unique filename
function generateUniqueFilename($originalName) {
    $extension = getFileExtension($originalName);
    $name = pathinfo($originalName, PATHINFO_FILENAME);
    return preg_replace('/[^a-z0-9]/i', '_', $name) . '_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
}

// Upload file — max 10 MB per file
function uploadFile($file, $uploadDir) {
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $maxFileSize       = 10 * 1024 * 1024; // 10 MB

    $fileExtension = getFileExtension($file['name']);
    $fileSize      = $file['size'];

    if (!in_array($fileExtension, $allowedExtensions)) {
        return ['success' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowedExtensions)];
    }

    if ($fileSize > $maxFileSize) {
        return ['success' => false, 'error' => 'File "' . htmlspecialchars($file['name']) . '" exceeds the 10 MB limit (' . round($fileSize / 1048576, 1) . ' MB).'];
    }

    $newFilename = generateUniqueFilename($file['name']);
    $uploadPath  = $uploadDir . $newFilename;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return ['success' => true, 'filename' => $newFilename, 'path' => $uploadPath];
    } else {
        return ['success' => false, 'error' => 'Failed to move uploaded file. Check folder permissions.'];
    }
}

// Delete file
function deleteFile($filePath) {
    if (file_exists($filePath) && is_file($filePath)) {
        return unlink($filePath);
    }
    return true;
}

// Format date
function formatDate($date) {
    return date('M d, Y h:i A', strtotime($date));
}

// Alert message
function setAlert($message, $type = 'success') {
    $_SESSION['alert'] = ['message' => $message, 'type' => $type];
}

// Display alert — now outputs a data attribute for the toast system
function displayAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        echo '<div id="phpAlertData"
                   data-message="' . htmlspecialchars($alert['message'], ENT_QUOTES) . '"
                   data-type="'    . htmlspecialchars($alert['type'],    ENT_QUOTES) . '"
                   style="display:none;"></div>';
        unset($_SESSION['alert']);
    }
}

// Get all clients
function getAllClients($conn, $active_only = true) {
    $query = "SELECT * FROM clients";
    if ($active_only) {
        $query .= " WHERE is_active = 1";
    }
    $query .= " ORDER BY sort_order ASC";
    $result = $conn->query($query);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// Get all services
function getAllServices($conn, $active_only = true) {
    $query = "SELECT * FROM services";
    if ($active_only) {
        $query .= " WHERE is_active = 1";
    }
    $query .= " ORDER BY sort_order ASC";
    $result = $conn->query($query);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// Get service by ID
function getServiceById($conn, $id) {
    $query = "SELECT * FROM services WHERE id = ?";
    $stmt  = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Get client by ID
function getClientById($conn, $id) {
    $query = "SELECT * FROM clients WHERE id = ?";
    $stmt  = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Count total records
function countRecords($conn, $table) {
    $query  = "SELECT COUNT(*) as count FROM " . $table;
    $result = $conn->query($query);
    $row    = $result->fetch_assoc();
    return $row['count'];
}

// JSON response
function jsonResponse($success, $message = '', $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data'    => $data
    ]);
    exit();
}
?>