<?php
// docs/admin/index.php
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit;
}

$page = $_GET['page'] ?? 'messages';

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Hope Worldwide</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --brand-green: #1b6c38;
            --brand-green-hover: #14522b;
            --brand-yellow: #f4c430;
            --bg-color: #f4f7f6;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            overflow-x: hidden;
        }
        /* Sidebar */
        #sidebar {
            width: 260px;
            background: var(--brand-green);
            min-height: 100vh;
            transition: all 0.3s;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        #sidebar.toggled {
            margin-left: -260px;
        }
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-header img {
            width: 80px;
            border-radius: 8px;
            background: white;
            padding: 5px;
        }
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 15px 25px;
            font-weight: 500;
            transition: 0.3s;
            display: flex;
            align-items: center;
        }
        .nav-link i {
            width: 25px;
            font-size: 1.1rem;
        }
        .nav-link:hover, .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            border-left: 4px solid var(--brand-yellow);
        }
        /* Main Content */
        #content {
            width: calc(100% - 260px);
            margin-left: 260px;
            transition: all 0.3s;
        }
        #content.expanded {
            width: 100%;
            margin-left: 0;
        }
        .topbar {
            background: white;
            padding: 15px 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        /* Custom Brand Buttons */
        .btn-brand {
            background-color: var(--brand-green);
            color: white;
            font-weight: 500;
        }
        .btn-brand:hover {
            background-color: var(--brand-green-hover);
            color: white;
        }
        
        @media (max-width: 768px) {
            #sidebar { margin-left: -260px; }
            #sidebar.toggled { margin-left: 0; }
            #content { width: 100%; margin-left: 0; }
        }
    </style>
</head>
<body>

    <nav id="sidebar" class="d-flex flex-column">
        <div class="sidebar-header">
            <img src="../res/logo.jpg" alt="Logo" onerror="this.src='https://placehold.co/80x80?text=Logo'">
            <h5 class="text-white mt-3 mb-0 fs-6">Admin Panel</h5>
        </div>
        
        <ul class="nav flex-column mb-auto mt-3">
            <li class="nav-item">
                <a href="?page=messages" class="nav-link <?= $page === 'messages' ? 'active' : '' ?>">
                    <i class="fas fa-envelope"></i> Messages
                </a>
            </li>
            <li class="nav-item">
                <a href="?page=events" class="nav-link <?= $page === 'events' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-alt"></i> Events
                </a>
            </li>
            <li class="nav-item">
                <a href="?page=meetings" class="nav-link <?= $page === 'meetings' ? 'active' : '' ?>">
                    <i class="fas fa-video"></i> Meetings
                </a>
            </li>
        </ul>
        
        <div class="p-3 border-top border-light border-opacity-10">
            <a href="?action=logout" class="nav-link text-danger justify-content-center">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>

    <div id="content">
        <div class="topbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button id="sidebarToggle" class="btn btn-light d-md-none me-3">
                    <i class="fas fa-bars"></i>
                </button>
                <h4 class="mb-0 text-success fw-bold" style="color: var(--brand-green) !important;">
                    <?php 
                    if($page === 'messages') echo 'Form Submissions';
                    if($page === 'events') echo 'Events Management';
                    if($page === 'meetings') echo 'Virtual Meetings';
                    ?>
                </h4>
            </div>
            <a href="../index.html" target="_blank" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-external-link-alt me-1"></i> View Site
            </a>
        </div>

        <div class="container-fluid p-4">
            <?php
            $file = __DIR__ . '/' . basename($page) . '.php'; // basic sanitization
            if (file_exists($file)) {
                include $file;
            } else {
                echo "<div class='alert alert-danger'>Page not found.</div>";
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('toggled');
            document.getElementById('content').classList.toggle('expanded');
        });
    </script>
</body>
</html>