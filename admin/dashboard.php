<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireLogin();

$page = isset($_GET['page']) ? sanitize($_GET['page']) : 'overview';
$stats = [
    'total_clients'   => countRecords($conn, 'clients'),
    'total_services'  => countRecords($conn, 'services'),
    'total_messages'  => countRecords($conn, 'contact_messages'),
    'total_supplies'  => countRecords($conn, 'supplies'),
    'total_updates'   => countRecords($conn, 'updates'),
    'unread_messages' => 0
];

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
    <style>
        .admin-layout { display:flex; height:100vh; overflow:hidden; }
        .admin-sidebar { width:250px; flex-shrink:0; background-color:var(--primary-color); color:#fff; height:100vh; overflow-y:auto; transition:transform .3s ease; z-index:1100; display:flex; flex-direction:column; }
        .sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:1090; }
        .sidebar-overlay.active { display:block; }
        .admin-body { flex:1; display:flex; flex-direction:column; overflow:hidden; min-width:0; }
        .admin-header { background:#fff; padding:.85rem 1.5rem; box-shadow:0 1px 4px rgba(0,0,0,.1); display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-shrink:0; z-index:10; }
        .admin-header h3 { margin:0; font-size:1.1rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .admin-top-right { display:flex; align-items:center; gap:.75rem; flex-shrink:0; }
        .sidebar-toggle-btn { display:none; background:var(--light-bg); border:1.5px solid var(--border-color); border-radius:8px; width:38px; height:38px; align-items:center; justify-content:center; cursor:pointer; color:var(--text-dark); font-size:1rem; transition:background .2s,border-color .2s; flex-shrink:0; }
        .sidebar-toggle-btn:hover { background:var(--primary-color); border-color:var(--primary-color); color:#fff; }
        .admin-main { flex:1; overflow-y:auto; background:var(--light-bg); padding:1.5rem; }
        .admin-nav-link { color:rgba(255,255,255,.85); padding:.82rem 1.4rem .82rem 1.2rem; margin:.18rem 0 .18rem .8rem; display:flex; align-items:center; gap:.75rem; text-decoration:none; transition:all .22s; border:none; font-weight:600; font-size:.93rem; border-radius:50px 0 0 50px; }
        .admin-nav-link:hover { background:rgba(255,255,255,.18); color:#fff; }
        .admin-nav-link.active { background:var(--light-bg); color:var(--primary-color)!important; font-weight:700; box-shadow:0 3px 12px rgba(0,0,0,.15); border-radius:50px 0 0 50px; }
        .admin-nav-link.active i { color:var(--primary-color)!important; }
        .admin-nav-link.logout-link { color:#ffcdd2!important; }
        .admin-nav-link.logout-link:hover { background:rgba(255,82,82,.2)!important; color:#ff5252!important; }
        @media (max-width:768px) {
            .admin-layout { flex-direction:column; height:100dvh; }
            .admin-sidebar { position:fixed; top:0; left:0; height:100dvh; transform:translateX(-100%); width:260px; }
            .admin-sidebar.active { transform:translateX(0); box-shadow:4px 0 24px rgba(0,0,0,.3); }
            .admin-body { width:100%; height:100dvh; }
            .sidebar-toggle-btn { display:flex; }
            .admin-header { padding:.75rem 1rem; }
            .admin-header h3 { font-size:1rem; }
            .admin-main { padding:1rem; }
        }
        @media (max-width:480px) {
            .admin-header h3 { font-size:.92rem; }
            .admin-top-right span { display:none; }
        }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="admin-layout">
    <!-- Sidebar -->
    <div class="admin-sidebar" id="adminSidebar">
        <div style="padding:1.4rem 1.5rem; border-bottom:1px solid rgba(255,255,255,0.15); display:flex; align-items:center; gap:.75rem; flex-shrink:0; background:var(--primary-color);">
            <img src="../css/assets/logo.png" alt="NAM Builders"
                 style="height:36px;width:auto;object-fit:contain;flex-shrink:0;"
                 onerror="this.style.display='none'">
            <div>
                <div style="font-family:'Barlow Condensed',sans-serif;font-weight:800;font-size:.95rem;color:#fff;line-height:1.2;">NAM Builders and Supply Corp</div>
                <div style="font-size:.68rem;color:rgba(255,255,255,.7);font-weight:600;letter-spacing:.06em;text-transform:uppercase;">Admin Panel</div>
            </div>
        </div>

        <nav style="padding:.75rem 0;flex:1;display:flex;flex-direction:column;">
            <div style="flex:1;">
                <a href="dashboard.php" class="admin-nav-link <?php echo $page==='overview'?'active':''; ?>">
                    <i class="fas fa-chart-line" style="width:16px;text-align:center;"></i> Overview
                </a>
                <a href="dashboard.php?page=clients" class="admin-nav-link <?php echo $page==='clients'?'active':''; ?>">
                    <i class="fas fa-users" style="width:16px;text-align:center;"></i> Clients
                </a>
                <a href="dashboard.php?page=services" class="admin-nav-link <?php echo $page==='services'?'active':''; ?>">
                    <i class="fas fa-cogs" style="width:16px;text-align:center;"></i> Services
                </a>
                <a href="dashboard.php?page=supplies" class="admin-nav-link <?php echo $page==='supplies'?'active':''; ?>">
                    <i class="fas fa-boxes" style="width:16px;text-align:center;"></i> Supplies
                    <?php if ($stats['total_supplies'] > 0): ?>
                        <span style="background:rgba(255,255,255,.18);color:rgba(255,255,255,.85);border-radius:50px;padding:0 .45rem;font-size:.68rem;font-weight:800;margin-left:auto;flex-shrink:0;">
                            <?php echo $stats['total_supplies']; ?>
                        </span>
                    <?php endif; ?>
                </a>
                <!-- ── Updates ── -->
                <a href="dashboard.php?page=updates" class="admin-nav-link <?php echo $page==='updates'?'active':''; ?>">
                    <i class="fas fa-newspaper" style="width:16px;text-align:center;"></i> Updates
                    <?php if ($stats['total_updates'] > 0): ?>
                        <span style="background:rgba(255,255,255,.18);color:rgba(255,255,255,.85);border-radius:50px;padding:0 .45rem;font-size:.68rem;font-weight:800;margin-left:auto;flex-shrink:0;">
                            <?php echo $stats['total_updates']; ?>
                        </span>
                    <?php endif; ?>
                </a>
                <!-- ── Stats ── -->
                <a href="dashboard.php?page=stats" class="admin-nav-link <?php echo $page==='stats'?'active':''; ?>">
                    <i class="fas fa-chart-bar" style="width:16px;text-align:center;"></i> Stats
                </a>
                <a href="dashboard.php?page=messages" class="admin-nav-link <?php echo $page==='messages'?'active':''; ?>">
                    <i class="fas fa-envelope" style="width:16px;text-align:center;"></i> Messages
                    <?php if ($stats['unread_messages'] > 0): ?>
                        <span style="background:#FFC107;color:#333;border-radius:50%;width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;font-size:.68rem;margin-left:auto;font-weight:800;flex-shrink:0;">
                            <?php echo $stats['unread_messages']; ?>
                        </span>
                    <?php endif; ?>
                </a>
            </div>
            <div style="padding-bottom:.5rem;">
                <hr style="margin:.5rem 0 .5rem .8rem;border-color:rgba(255,255,255,0.2);">
                <a href="../backend/logout.php" class="admin-nav-link logout-link">
                    <i class="fas fa-sign-out-alt" style="width:16px;text-align:center;"></i> Logout
                </a>
            </div>
        </nav>
    </div>

    <!-- Main content -->
    <div class="admin-body">
        <div class="admin-header">
            <div style="display:flex;align-items:center;gap:.75rem;min-width:0;">
                <button class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Open menu">
                    <i class="fas fa-bars"></i>
                </button>
                <h3>
                    <?php
                    switch($page) {
                        case 'clients':  echo 'Manage Clients';   break;
                        case 'services': echo 'Manage Services';  break;
                        case 'supplies': echo 'Manage Supplies';  break;
                        case 'updates':  echo 'Updates &amp; Posts'; break;
                        case 'stats':    echo 'Stats Section';    break;
                        case 'messages': echo 'Contact Messages'; break;
                        default:         echo 'Dashboard Overview';
                    }
                    ?>
                </h3>
            </div>
            <div class="admin-top-right">
                <span style="font-size:.88rem;color:var(--text-light);">
                    Welcome, <strong style="color:var(--text-dark);"><?php echo sanitize($_SESSION['admin_username']); ?></strong>
                </span>
                <?php if ($stats['unread_messages'] > 0): ?>
                    <a href="dashboard.php?page=messages"
                       style="background:#FFC107;color:#333;border-radius:50px;padding:.25rem .7rem;font-size:.78rem;font-weight:800;text-decoration:none;display:flex;align-items:center;gap:.35rem;">
                        <i class="fas fa-envelope"></i>
                        <?php echo $stats['unread_messages']; ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="admin-main">
            <div class="container-lg" style="max-width:1200px;">
                <?php
                switch($page) {
                    case 'clients':  require 'pages/clients.php';  break;
                    case 'services': require 'pages/services.php'; break;
                    case 'supplies': require 'pages/supplies.php'; break;
                    case 'updates':  require 'pages/updates.php';  break;
                    case 'stats':    require 'pages/stats.php';    break;
                    case 'messages': require 'pages/messages.php'; break;
                    default:         require 'pages/overview.php';
                }
                ?>
            </div>
        </div>
    </div>
</div>
<!-- ═══════════════════════════════════════════════════════════════
     TOAST NOTIFICATION SYSTEM
     Paste this block just before the closing </body> tag in
     admin/dashboard.php  (replace the existing inline <script> block
     or add it right after the </div> that closes .admin-layout)
     ═══════════════════════════════════════════════════════════════ -->

<!-- Toast container (fixed top-right) -->
<div id="toastContainer" style="
    position: fixed;
    top: 1.2rem;
    right: 1.2rem;
    z-index: 99999;
    display: flex;
    flex-direction: column;
    gap: .65rem;
    pointer-events: none;
    max-width: 360px;
    width: calc(100vw - 2.4rem);
"></div>

<style>
/* ── Toast card ── */
.adm-toast {
    display: flex;
    align-items: flex-start;
    gap: .75rem;
    background: #fff;
    border-radius: 12px;
    padding: .9rem 1rem .9rem 1rem;
    box-shadow: 0 8px 32px rgba(0,0,0,.18), 0 2px 8px rgba(0,0,0,.1);
    pointer-events: all;
    position: relative;
    overflow: hidden;
    /* slide-in from right */
    animation: toastSlideIn .35s cubic-bezier(.22,.68,0,1.1) both;
    border-left: 4px solid #ccc;
    min-width: 260px;
}
.adm-toast.toast-success { border-left-color: #28A745; }
.adm-toast.toast-danger  { border-left-color: #DC3545; }
.adm-toast.toast-warning { border-left-color: #FFC107; }
.adm-toast.toast-info    { border-left-color: #1565C0; }

@keyframes toastSlideIn {
    from { opacity: 0; transform: translateX(60px) scale(.96); }
    to   { opacity: 1; transform: translateX(0)    scale(1);   }
}
@keyframes toastSlideOut {
    from { opacity: 1; transform: translateX(0)    scale(1);   max-height: 120px; margin-bottom: 0; }
    to   { opacity: 0; transform: translateX(60px) scale(.96); max-height: 0;     margin-bottom: -.65rem; }
}
.adm-toast.removing {
    animation: toastSlideOut .35s ease forwards;
    pointer-events: none;
}

/* Icon circle */
.adm-toast-icon {
    width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .95rem; flex-shrink: 0; margin-top: .05rem;
}
.toast-success .adm-toast-icon { background: #D4EDDA; color: #28A745; }
.toast-danger  .adm-toast-icon { background: #F8D7DA; color: #DC3545; }
.toast-warning .adm-toast-icon { background: #FFF3CD; color: #856404; }
.toast-info    .adm-toast-icon { background: #D1ECF1; color: #1565C0; }

/* Text */
.adm-toast-body { flex: 1; min-width: 0; }
.adm-toast-title {
    font-size: .78rem; font-weight: 800; letter-spacing: .06em;
    text-transform: uppercase; margin-bottom: .18rem;
}
.toast-success .adm-toast-title { color: #28A745; }
.toast-danger  .adm-toast-title { color: #DC3545; }
.toast-warning .adm-toast-title { color: #856404; }
.toast-info    .adm-toast-title { color: #1565C0; }

.adm-toast-msg {
    font-size: .86rem; color: #374151; line-height: 1.5;
    word-break: break-word;
}

/* Close button */
.adm-toast-close {
    background: none; border: none; color: #9CA3AF;
    font-size: 1.1rem; cursor: pointer; padding: 0; line-height: 1;
    flex-shrink: 0; transition: color .2s; margin-top: -.1rem;
}
.adm-toast-close:hover { color: #374151; }

/* Progress bar (counts down 5 s) */
.adm-toast-progress {
    position: absolute; bottom: 0; left: 0; right: 0;
    height: 3px; background: rgba(0,0,0,.06);
}
.adm-toast-progress-fill {
    height: 100%; width: 100%;
    transition: width linear;
}
.toast-success .adm-toast-progress-fill { background: #28A745; }
.toast-danger  .adm-toast-progress-fill { background: #DC3545; }
.toast-warning .adm-toast-progress-fill { background: #FFC107; }
.toast-info    .adm-toast-progress-fill { background: #1565C0; }
</style>

<script>
/* ════════════════════════════════════════════════════════════════
   TOAST SYSTEM
   Usage (from anywhere in the admin):
     showToast('Your message', 'success')   // success | danger | warning | info
     showToast('Upload failed', 'danger')
   ════════════════════════════════════════════════════════════════ */
(function () {

    var DURATION = 5000; // ms before auto-dismiss

    var icons = {
        success: 'fas fa-check-circle',
        danger:  'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info:    'fas fa-info-circle'
    };
    var titles = {
        success: 'Success',
        danger:  'Error',
        warning: 'Warning',
        info:    'Info'
    };

    window.showToast = function (message, type) {
        type = type || 'info';

        var container = document.getElementById('toastContainer');
        if (!container) return;

        var toast = document.createElement('div');
        toast.className = 'adm-toast toast-' + type;

        toast.innerHTML =
            '<div class="adm-toast-icon"><i class="' + icons[type] + '"></i></div>' +
            '<div class="adm-toast-body">' +
                '<div class="adm-toast-title">' + (titles[type] || 'Notice') + '</div>' +
                '<div class="adm-toast-msg">'  + message + '</div>' +
            '</div>' +
            '<button class="adm-toast-close" aria-label="Dismiss">&times;</button>' +
            '<div class="adm-toast-progress">' +
                '<div class="adm-toast-progress-fill"></div>' +
            '</div>';

        container.appendChild(toast);

        /* close button */
        toast.querySelector('.adm-toast-close').addEventListener('click', function () {
            removeToast(toast);
        });

        /* progress bar animation — start after a short delay so the
           browser has painted the element at width:100% first */
        var fill = toast.querySelector('.adm-toast-progress-fill');
        setTimeout(function () {
            fill.style.transition = 'width ' + DURATION + 'ms linear';
            fill.style.width = '0%';
        }, 30);

        /* auto-dismiss */
        var timer = setTimeout(function () { removeToast(toast); }, DURATION);

        /* pause on hover */
        toast.addEventListener('mouseenter', function () { clearTimeout(timer); fill.style.transitionDuration = '0ms'; });
        toast.addEventListener('mouseleave', function () {
            var remaining = (parseFloat(fill.style.width) / 100) * DURATION;
            fill.style.transition = 'width ' + remaining + 'ms linear';
            fill.style.width = '0%';
            timer = setTimeout(function () { removeToast(toast); }, remaining);
        });
    };

    function removeToast(toast) {
        toast.classList.add('removing');
        toast.addEventListener('animationend', function () {
            if (toast.parentNode) toast.parentNode.removeChild(toast);
        });
    }

    /* ── Pick up PHP setAlert() messages rendered by displayAlert() ── */
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('phpAlertData');
        if (el) {
            var msg  = el.getAttribute('data-message');
            var type = el.getAttribute('data-type') || 'info';
            if (msg) showToast(msg, type);
            el.parentNode.removeChild(el);
        }
    });

}());
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/admin.js"></script>
<script>
(function () {
    var sidebar   = document.getElementById('adminSidebar');
    var overlay   = document.getElementById('sidebarOverlay');
    var toggleBtn = document.getElementById('sidebarToggleBtn');
    var closeBtn  = document.getElementById('sidebarCloseBtn');

    function openSidebar()  { sidebar.classList.add('active'); overlay.classList.add('active'); document.body.style.overflow='hidden'; }
    function closeSidebar() { sidebar.classList.remove('active'); overlay.classList.remove('active'); document.body.style.overflow=''; }

    toggleBtn.addEventListener('click', openSidebar);
    closeBtn.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);

    sidebar.querySelectorAll('.admin-nav-link').forEach(function(link) {
        link.addEventListener('click', function() { if (window.innerWidth <= 768) closeSidebar(); });
    });

    document.addEventListener('keydown', function(e) { if (e.key==='Escape') closeSidebar(); });
}());
</script>
</body>
</html>