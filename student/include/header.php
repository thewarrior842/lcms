<?php
require_once('config.php');
require('auth.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <a href="index.php">
                <i class="fas fa-graduation-cap"></i><h1>ATTC</h1>
                    </a>
            </div>

            <div class="nav-menu">
                
                <div class="nav-item">
                    <a href="sinfo.php">
                       <i class="fas fa-info-circle"></i>
                        <span>Profile Info</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="chgpwd.php">
                        <i class="fas fa-exchange-alt"></i>
                        <span>Change Password</span>
                    </a>
                </div>

                <li class="nav-item logout">
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </div>

            <div class="student-info">
                <div class="student-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <?php
                try {
                    $src = "SELECT s.*, d.* FROM student s INNER JOIN dept d ON s.did=d.did";
                    $rs = $con->query($src);
                    $rec = $rs->fetch_assoc();
                } catch (mysqli_sql_exception $e) {
                    echo $e->getMessage();
                }
                ?>
                <div class="student-name">
                    <p><?= $_SESSION['s_info']['fname'] . " " . $_SESSION['s_info']['mname'] . " " . $_SESSION['s_info']['lname'] ?? 'Student'; ?></p>
                </div>
                <div class="student-id">
                    <p><?= $_SESSION['s_info']['rollno'] ?? 'Rollno'; ?></p>
                </div>
                <?php
                $rollno = $_SESSION['s_info']['rollno'] ?? '';
                if ($rollno) {
                    $stmt = $con->prepare("SELECT s.*, d.dname 
                           FROM student s 
                           INNER JOIN dept d ON s.did = d.did 
                           WHERE s.rollno = ?");
                    $stmt->bind_param("s", $rollno);
                    $stmt->execute();
                    $rec = $stmt->get_result()->fetch_assoc();
                    // Now use $rec['dname'] directly
                }
                ?>
                <div class="student-program">
                    <p><?= htmlspecialchars($rec['dname'] ?? 'Department Name'); ?></p>
                </div>
            </div>
        </div>