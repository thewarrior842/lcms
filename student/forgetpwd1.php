<?php
require('config.php');
// Set the default timezone to India Standard Time (Asia/Kolkata)
date_default_timezone_set('Asia/Kolkata');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="Description" content="HOD Forget Password System">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Forget Password</title>
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
                        <h1 class="mb-3"><i class="fas fa-lock"></i> Student Forgot Password Portal</h1>
                        <p class="mb-0">Secure Access for Administrators</p>
                    </div>

                    <div class="login-form">
                        <p class="text-success">Your verification code is send in your mail </p>
                        <form name="frm" method="post" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label for="email_id" class="form-label fw-bold">
                                    <i class="fas fa-envelope me-2"></i>Enter your new password
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="password"
                                        class="form-control form-control-lg"
                                        id="pwd"
                                        name="pwd"
                                        placeholder="Enter new password"
                                        required>
                                    <div class="invalid-feedback">
                                        Please enter a valid email address.
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="email_id" class="form-label fw-bold">
                                    <i class="fas fa-envelope me-2"></i>Enter your confirm password
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="password"
                                        class="form-control form-control-lg"
                                        id="cpwd"
                                        name="cpwd"
                                        placeholder="Enter confirm password"
                                        required>
                                    <div class="invalid-feedback">
                                        Please enter a valid email address.
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="email_id" class="form-label fw-bold">
                                    <i class="fas fa-envelope me-2"></i>Enter your verification code
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text"
                                        class="form-control form-control-lg"
                                        id="code"
                                        name="code"
                                        placeholder="Enter new password"
                                        required>
                                    <div class="invalid-feedback">
                                        Please enter a valid email address.
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" name="cp" class="btn login-btn btn-lg text-white">
                                    <i class="fas fa-user-check"></i>Change Password
                                </button>
                            </div>
                        </form>


                        <?php
                        if (isset($_POST['cp'])) {
                            $pwd = $_POST['pwd'];
                            $cpwd = $_POST['cpwd'];
                            $postcode = $_POST['code'];
                            $code = $_SESSION['v_code'];
                            $email_id =$_GET['email_id'];
                            if (empty($pwd) && empty($cpwd) && empty($postcode)) {
                                echo $error = "Please fill all the criteria";
                            } else {
                                if ($code == $postcode) {
                                    if ($pwd == $cpwd) {
                                        try {
                                            $pwd = password_hash($pwd, PASSWORD_DEFAULT);
                                            $upd = "UPDATE student SET pwd='$pwd' WHERE email_id='$email_id'";
                                            $con->query($upd);
                                            unset($_SESSION['v_code']);
                        ?>
                                            <script>
                                                alert("Password changed successfully");
                                                window.location = "login.php";
                                            </script>
                        <?php
                                        } catch (mysqli_sql_exception $e) {
                                            echo $error = $e->getMessage();
                                        }
                                    } else {
                                        echo $error = "Does not match from new password";
                                    }
                                } else {
                                    echo $error = "Invalid verification code";
                                }
                            }
                        }
                        ?>
                    </div>

                    <div class="card-footer text-center py-3">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-2"></i>Secure login system •
                            <i class="fas fa-clock ms-2 me-2"></i>Session timeout: 30 min
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.0/js/bootstrap.min.js"></script>

</body>

</html>