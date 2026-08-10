<?php
require_once('config.php');
require('auth.php'); // Security check
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Admin Dashboard">

    <title>Admin Dashboard</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.0/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="asstees/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        function myhod() {
            alert('Hi');
        }
    </script>

</head>

<body>

    <!-- =========================
     SIDEBAR
    ========================= -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <h3><i class="fas fa-crown me-2"></i>Admin Panel</h3>
            <p><?= $_SESSION['a_info']['role'] ?? 'Administrator'; ?></p>
        </div>

        <ul class="nav flex-column">

            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link active" href="index.php">
                    <span>
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-text">Dashboard</span>
                    </span>
                </a>
            </li>

            <!-- Users (Submenu) -->
            <li class="nav-item">
                <a class="nav-link"
                    data-bs-toggle="collapse"
                    href="#usersSubmenu"
                    role="button"
                    aria-expanded="false"
                    aria-controls="usersSubmenu">
                    <span>
                        <i class="fas fa-users"></i>
                        <span class="nav-text">Users</span>
                    </span>
                    <i class="fas fa-chevron-down"></i>
                </a>

                <ul class="collapse nav flex-column" id="usersSubmenu">
                    <li class="nav-item">
                        <a class="nav-link" href="hod.php">
                            <i class="fas fa-users"></i> All Teacher/Faculty's
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gatekeeper.php">
                            <i class="fas fa-user-shield"></i> All Gatekeepers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="assign_incharge.php">
                            <i class="fas fa-user-shield"></i> Assign Section Incharges
                        </a>
                    </li>
                    <!--<li class="nav-item">
                        <a class="nav-link" href="view_incharge_students.php">
                            <i class="fas fa-user-shield"></i> View Section Incharges
                        </a>
                    </li>-->
                    <li class="nav-item">
                        <a class="nav-link" href="upd_incharge_students.php">
                            <i class="fas fa-user-shield"></i> Update Section Incharges
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Department -->
            <li class="nav-item">
                <a class="nav-link" href="dept.php">
                    <span>
                        <i class="fas fa-building"></i>
                        <span class="nav-text">Department</span>
                    </span>
                </a>
            </li>
            
            <!-- Update entry -->
            <li class="nav-item">
                <a class="nav-link" href="editentry.php">
                    <span>
                        <i class="fas fa-edit"></i>
                        <span class="nav-text">Update Entry</span>
                    </span>
                </a>
            </li>

            <!-- Reports -->
            <!-- <li class="nav-item">
                <a class="nav-link" href="report.php">
                    <span>
                        <i class="fas fa-file-alt"></i>
                        <span class="nav-text">Reports</span>
                    </span>
                </a>
            </li> -->

            <!-- Settings (Submenu) -->
            <li class="nav-item">
                <a class="nav-link"
                    data-bs-toggle="collapse"
                    href="#settingsSubmenu"
                    role="button"
                    aria-expanded="false"
                    aria-controls="settingsSubmenu">
                    <span>
                        <i class="fas fa-cog"></i>
                        <span class="nav-text">Settings</span>
                    </span>
                    <i class="fas fa-chevron-down"></i>
                </a>

                <ul class="collapse nav flex-column" id="settingsSubmenu">
                    <!--<li class="nav-item">
                        <a class="nav-link" href="profile-settings.php">
                            <i class="fas fa-user-edit"></i> Profile Settings
                        </a>
                    </li>-->
            </li>
            <li class="nav-item">
                <a class="nav-link" href="chngPass.php">
                    <i class="fas fa-exchange-alt"></i> Change Password
                </a>
            </li>
        </ul>
        </li>

        <!-- Logout -->
        <li class="nav-item">
            <a class="nav-link" href="logout.php">
                <span>
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="nav-text">Logout</span>
                </span>
            </a>
        </li>

        </ul>
    </nav>