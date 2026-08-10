<?php
require_once('config.php');
require('auth.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Teacher/Faculty Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Additional responsive styles for mobile menu – add to your style.css if preferred */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: var(--primary, #2c3e50);
            color: white;
            padding: 10px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.5rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
            display: none;
        }
        .overlay.active {
            display: block;
        }
        @media (max-width: 768px) {
            .mobile-toggle {
                display: block;
            }
            .sidebar {
                display: block !important;
                position: fixed;
                left: -280px;
                top: 0;
                width: 280px;
                height: 100%;
                transition: left 0.3s ease;
                z-index: 1050;
            }
            .sidebar.active {
                left: 0;
            }
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                padding-top: 70px !important;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile menu toggle button -->
    <div class="mobile-toggle" id="mobileToggle">
        <i class="fas fa-bars"></i>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logo-container">
            <div class="logo">
                <i class="fas fa-university"></i>
                <h1>Teacher/Faculty <span>Dashboard</span></h1>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="index.php" class="active"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li><a href="addstudent.php"><i class="fas fa-user-graduate"></i> <span>Add Students</span></a></li>
            <li><a href="updstd.php"><i class="fas fa-user-edit"></i><span>Update Students</span></a></li>
            <li><a href="entrylog.php"><i class="fas fa-dungeon"></i> <span>Late Entry Logs</span></a></li>
            <li><a href="attenview.php"><i class="fas fa-calendar-alt"></i> <span>Late Count</span></a></li>
            <li><a href="no_id_card.php"><i class="fas fa-id-card"></i><span>No ID Card Entry</span></a></li>
            <li><a href="tinfo.php"><i class="fas fa-info-circle"></i><span>Profile info</span></a></li>
            <li><a href="chngPass.php"><i class="fas fa-exchange-alt"></i><span>Change Password</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
        </ul>
    </div>

    <script>
        // Mobile menu toggle functionality
        (function() {
            const toggleBtn = document.getElementById('mobileToggle');
            const sidebar = document.getElementById('sidebar');
            
            // Create overlay element if not present
            let overlay = document.querySelector('.overlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.className = 'overlay';
                document.body.appendChild(overlay);
            }
            
            function toggleSidebar() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
                // Prevent body scroll when sidebar is open
                document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
            }
            
            if (toggleBtn) {
                toggleBtn.addEventListener('click', toggleSidebar);
            }
            
            overlay.addEventListener('click', toggleSidebar);
            
            // Close sidebar when a nav link is clicked (better UX on mobile)
            const navLinks = document.querySelectorAll('.nav-links a');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 768 && sidebar.classList.contains('active')) {
                        toggleSidebar();
                    }
                });
            });
            
            // Reset sidebar state when resizing above mobile breakpoint
            window.addEventListener('resize', () => {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        })();
    </script>
</body>
</html>