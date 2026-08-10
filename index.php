<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="Description" content="Login portal for different user roles">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            max-width: 600px;
            width: 100%;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        h1 {
            color: #333;
            font-weight: 600;
            margin-bottom: 30px;
            text-align: center;
            font-size: 2.2rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        .role-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }
        .btn-role {
            flex: 1 1 calc(50% - 15px);
            min-width: 150px;
            padding: 20px;
            border-radius: 15px;
            font-size: 1.2rem;
            font-weight: 500;
            text-decoration: none;
            color: white;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: none;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        .btn-role:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
            color: white;
        }
        .btn-admin { background: #dc3545; }      /* Red */
        .btn-teacher { background: #28a745; }    /* Green */
        .btn-gatekeeper { background: #ffc107; color: #333; } /* Yellow with dark text */
        .btn-student { background: #17a2b8; }     /* Teal */
        .btn-admin:hover { background: #c82333; }
        .btn-teacher:hover { background: #218838; }
        .btn-gatekeeper:hover { background: #e0a800; color: #333; }
        .btn-student:hover { background: #138496; }
        .footer-note {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 0.9rem;
        }
        .footer-note i {
            color: #e25555;
        }
        @media (max-width: 480px) {
            .login-card { padding: 30px 20px; }
            h1 { font-size: 1.8rem; }
            .btn-role { flex: 1 1 100%; }
        }
    </style>
    <title>Login Portal</title>
</head>
<body>
    <div class="login-card">
        <h1><i class="fas fa-user-lock me-2"></i>Login as</h1>
        <div class="role-buttons">
            <a href="admin/login.php" class="btn-role btn-admin">
                <i class="fas fa-user-tie fa-lg"></i> Admin
            </a>
            <a href="hod/login.php" class="btn-role btn-teacher">
                <i class="fas fa-chalkboard-teacher fa-lg"></i> Teacher/Faculty
            </a>
            <a href="gatekeeper/login.php" class="btn-role btn-gatekeeper">
                <i class="fas fa-shield-alt fa-lg"></i> Gatekeeper
            </a>
            <a href="student/login.php" class="btn-role btn-student">
                <i class="fas fa-user-graduate fa-lg"></i> Student
            </a>
        </div>
        <div class="footer-note">
            <i class="fas fa-lock me-1"></i> Secure login portal · Choose your role · Developed by: Deep Karmakar
        </div>
    </div>

    <!-- Bootstrap JS (optional for interactivity, but not required for this page) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.0/js/bootstrap.min.js"></script>
</body>
</html>