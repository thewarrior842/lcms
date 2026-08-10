<?php
use FFI\Exception;
require('config.php');
date_default_timezone_set('Asia/Kolkata');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="Description" content="Student Login System">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Student Login</title>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(to right, #4e73df, #224abe);
            color: white;
            padding: 30px 20px;
        }

        .login-form {
            padding: 40px;
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }

        .login-btn {
            background: linear-gradient(to right, #4e73df, #224abe);
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .error-message {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-10px);
            }

            75% {
                transform: translateX(10px);
            }
        }

        .input-group-text {
            border-right: 0;
        }

        .password-toggle {
            border-left: 0;
            cursor: pointer;
        }

        .password-toggle:hover {
            background-color: #e9ecef;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-card">
                    <div class="login-header text-center">
                        <h1 class="mb-3"><i class="fas fa-lock"></i> Student Portal</h1>
                        <p class="mb-0">Secure Access for Students</p>
                    </div>

                    <div class="login-form">
                        <form name="frm" method="post" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label for="email_id" class="form-label fw-bold">
                                    <i class="fas fa-envelope me-2"></i>Email Address
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="email"
                                        class="form-control form-control-lg"
                                        id="email_id"
                                        name="email_id"
                                        placeholder="student@example.com"
                                        required>
                                    <div class="invalid-feedback">
                                        Please enter a valid email address.
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="pwd" class="form-label fw-bold">
                                    <i class="fas fa-key me-2"></i>Password
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>

                                    <input type="password"
                                        class="form-control form-control-lg"
                                        id="pwd"
                                        name="pwd"
                                        placeholder="Enter your password"
                                        required>

                                    <!-- Password toggle button -->
                                    <button class="input-group-text password-toggle"
                                        type="button"
                                        id="togglePassword">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </button>

                                    <div class="invalid-feedback">
                                        Please enter your password.
                                    </div>
                                </div>

                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="ok" class="btn login-btn btn-lg text-white">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </button>
                            </div>

                            <div class="text-center mt-4">
                                <a href="forgetpwd.php" class="text-decoration-none">
                                    <i class="fas fa-question-circle me-2"></i>Forgot Password?
                                </a>
                            </div>
                        </form>

                        <?php
                        if (isset($_POST['ok'])) {
                            $email_id = $_POST['email_id'];
                            $pwd = $_POST['pwd'];
                            $error = null;
                            try {
                                $src = "SELECT * FROM student WHERE email_id='$email_id'";
                                $rs = $con->query($src);
                                if ($rs->num_rows > 0) {
                                    $row = $rs->fetch_assoc();
                                    if (password_verify($pwd, $row['pwd'])) {
                                        unset($row['pwd']);
                                        $_SESSION['s_info']=$row;
                                        ?>
                                        <script>
                                            window.location='index.php';
                                        </script>
                                        <?php
                                    } else {
                                        $error = "Invalid Password or email id";
                                    }
                                } else {
                                    $error = "Invalid Email or password";
                                }
                            } catch (Exception $e) {
                                $error = "Error: " . $e->getMessage();
                            }

                            if ($error) {
                                echo '<div class="alert alert-danger error-message mt-4 alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-triangle me-2"></i>' . $error . '
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                      </div>';
                            }
                        }
                        ?>
                    </div>

                    <div class="card-footer text-center py-3">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-2"></i>Secure login system •
                            <i class="fas fa-clock ms-2 me-2"></i>Session timeout: 30 min •<br>
                            <i class="fas fa-user-secret"></i> Developed by: Deep Karmakar
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.0/js/bootstrap.min.js"></script>
    
    <!-- Password Toggle JavaScript -->
    <script>
        // Wait for the DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Get the password input field and the toggle button
            const passwordField = document.getElementById('pwd');
            const toggleButton = document.getElementById('togglePassword');
            const toggleIcon = document.getElementById('toggleIcon');
            
            // Add click event listener to the toggle button
            toggleButton.addEventListener('click', function() {
                // Check the current type of the password field
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                // Set the new type
                passwordField.setAttribute('type', type);
                
                // Toggle the eye icon
                if (type === 'text') {
                    // Password is visible: show open eye (or eye-slash to indicate visibility)
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                } else {
                    // Password is hidden: show closed eye
                    toggleIcon.classList.remove('fa-eye-slash');
                    toggleIcon.classList.add('fa-eye');
                }
            });
        });
    </script>
</body>

</html>