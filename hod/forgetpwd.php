<?php
require('config.php');
// Set the default timezone to India Standard Time (Asia/Kolkata)
date_default_timezone_set('Asia/Kolkata');


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);
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
                        <h1 class="mb-3"><i class="fas fa-lock"></i> Teacher/Faculty Forgot password Portal</h1>
                        <p class="mb-0">Secure Access for Administrators</p>
                    </div>

                    <div class="login-form">
                        <form name="ev-frm" id="ev-frm" method="post" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label for="email_id" class="form-label fw-bold">
                                    <i class="fas fa-envelope me-2"></i>Enter your Email Address
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="email"
                                        class="form-control form-control-lg"
                                        id="email_id"
                                        name="email_id"
                                        placeholder="teacher@example.com"
                                        required>
                                    <div class="invalid-feedback">
                                        Please enter a valid email address.
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="ok" class="btn login-btn btn-lg text-white" onclick="hideForm();">
                                    <i class="fas fa-user-check"></i> Verify
                                </button>
                            </div>
                        </form>
                        <?php
                        if (isset($_POST['ok'])) {
                            $email_id = $_POST['email_id'];
                            $src = "SELECT email_id FROM user WHERE email_id='$email_id'";
                            $rs = $con->query($src);
                            if ($rs->num_rows > 0) {
                                $code = rand(000000, 999999);
                                $_SESSION['v_code']=$code;
                                try {
                                    // Server settings
                                    $mail->isSMTP();
                                    $mail->Host       = 'smtp.gmail.com'; // Gmail SMTP server
                                    $mail->SMTPAuth   = true;
                                    $mail->Username   = 'attcprincipal22@gmail.com';  // YOUR Gmail address
                                    $mail->Password   = 'cfvg izsc mqxz qfli';     // YOUR Gmail app password
                                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                                    $mail->Port       = 587;
                                    $mail->setFrom('attcprincipal22@gmail.com', 'ATTC HOD');
                                    $semail = $email_id;
                                    $sname = $email_id;
                                    // $spassword = $p;

                                    $mail->addAddress($semail, $sname);

                                    // Content
                                    $mail->isHTML(false);
                                    $mail->Subject = 'Your verification code';
                                    $mail->Body    = "Dear $sname,\nThis email is for verification of your Email ID.\n\nOTP for Email verification: $code. Please do not share with anyone.\n\nRegards,\nATTC HOD Team";
                                    $mail->send();
                                    echo "Verfiction code has been send in your mail";
                                    ?>
                                    <script>
                                        window.location='forgetpwd1.php?email_id=<?php echo $email_id ?>';
                                    </script>
                                    <?php
                                } catch (Exception $e) {
                                    echo $e->getMessage();
                                }
                            } else {
                                $error = "Your email-id does not exists";
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