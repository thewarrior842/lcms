<?php
require_once('config.php');
require('auth.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gatekeeper Security Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="asstes/style.css">
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="logo">
                <h1><i class="fas fa-shield-alt"></i> Gatekeeper Pro</h1>
                <p>Welcome back, <?php echo $_SESSION['g_info']['gfname'] . " " . $_SESSION['g_info']['glname']; ?>!</p>
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="index.php" class="nav-link active">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            </ul>
            <!-- Settings (Submenu) -->
            <ul class="nav-links">
                <li class="nav-item">
                    <a href="#" class="nav-link" data-toggle="collapse" data-target="#gatekeeperSubmenu">
                        <i class="fas fa-shield-alt"></i>
                        <span>Gatekeeper</span>
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="collapse" id="gatekeeperSubmenu">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="ver_entry.php" class="nav-link">
                                    <i class="fas fa-user-check"></i>
                                    <span>Verify Entries</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="entry_log.php" class="nav-link">
                                    <i class="fas fa-clock"></i>
                                    <span>Entry Logs</span>
                                </a>
                            </li>
                            
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
                <!-- other nav items -->
            </ul>

        </nav>
        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <h2>Gatekeeper Dashboard</h2>
                <div class="user-info">
                    <div class="user-avatar">GS</div>
                    <div>
                        <div style="font-weight: 600;">Gatekeeper System</div>
                        <div style="font-size: 0.9rem; color: var(--gray-color);">Admin</div>
                    </div>
                </div>
            </div>