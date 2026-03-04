<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
requireLogin();

$page = isset($_GET['page']) ? sanitize($_GET['page']) : 'overview';
$stats = [
    'total_clients' => countRecords($conn, 'clients'),
    'total_services' => countRecords($conn, 'services'),
    'total_messages' => countRecords($conn, 'contact_messages'),
    'unread_messages' => 0
];

// Get unread message count
$result = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0");
if ($result) {
    $row = $result->fetch_assoc();
    $stats['unread_messages'] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - NAM Builders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div style="display: flex; height: 100vh;">
        <!-- Sidebar -->
        <div class="admin-sidebar" style="width: 250px; background-color: var(--primary-color); color: white; overflow-y: auto;">
            <div style="padding: 1.5rem 2rem; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: .75rem;">
                <img src="../css/assets/logo.png"
                     alt="NAM Builders"
                     style="height: 38px; width: auto; object-fit: contain; flex-shrink: 0;"
                     onerror="this.style.display='none'">
                <div>
                    <div style="font-family:'Barlow Condensed',sans-serif; font-weight:800; font-size:1rem; color:#fff; line-height:1.2;">
                        NAM Builders
                    </div>
                    <div style="font-size:.72rem; color:rgba(255,255,255,.65); font-weight:600; letter-spacing:.04em; text-transform:uppercase;">
                        Admin Panel
                    </div>
                </div>
            </div>

            <nav style="padding: 1rem 0;">
                <a href="dashboard.php" class="admin-nav-link <?php echo $page === 'overview' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i> Overview
                </a>
                <a href="dashboard.php?page=clients" class="admin-nav-link <?php echo $page === 'clients' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Clients
                </a>
                <a href="dashboard.php?page=services" class="admin-nav-link <?php echo $page === 'services' ? 'active' : ''; ?>">
                    <i class="fas fa-cogs"></i> Services
                </a>
                <a href="dashboard.php?page=messages" class="admin-nav-link <?php echo $page === 'messages' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i> Messages
                    <?php if ($stats['unread_messages'] > 0): ?>
                        <span style="background-color: #FFC107; color: #333; border-radius: 50%; width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.72rem; margin-left: auto; font-weight: 800;">
                            <?php echo $stats['unread_messages']; ?>
                        </span>
                    <?php endif; ?>
                </a>
                <hr style="margin: 1rem 0; border-color: rgba(255,255,255,0.1);">
                <a href="../backend/logout.php" class="admin-nav-link" style="color: #FF6B6B;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div style="flex: 1; display: flex; flex-direction: column;">
            <!-- Top Header -->
            <div class="admin-header">
                <h3 style="margin: 0;">
                    <?php 
                    switch($page) {
                        case 'clients': echo 'Manage Clients'; break;
                        case 'services': echo 'Manage Services'; break;
                        case 'messages': echo 'Contact Messages'; break;
                        default: echo 'Dashboard Overview';
                    }
                    ?>
                </h3>
                <div class="admin-top-nav">
                    <span>Welcome, <strong><?php echo sanitize($_SESSION['admin_username']); ?></strong></span>
                </div>
            </div>

            <!-- Content Area -->
            <div class="admin-main" style="flex: 1; overflow-y: auto;">
                <div class="container-lg">
                    <?php
                    switch($page) {
                        case 'clients':
                            require 'pages/clients.php';
                            break;
                        case 'services':
                            require 'pages/services.php';
                            break;
                        case 'messages':
                            require 'pages/messages.php';
                            break;
                        default:
                            require 'pages/overview.php';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/admin.js"></script>
</body>
</html>