<?php
require('include/header.php');
require_once('config.php');
try {
    $email_id = $_SESSION['a_info']['email_id'];
    $src = "SELECT pwd FROM user WHERE email_id='$email_id'";
    $rs = $con->query($src);
    $row = $rs->fetch_assoc();
} catch (mysqli_sql_exception $e) {
    $error = "Error: " . $e->getMessage();
}
$error = "";
$msg = "";
?>
<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Change Password</h2>
            <p class="text-muted mb-0">Welcome back, <?php echo $_SESSION['a_info']['afname'] . " " . $_SESSION['a_info']['alname']; ?>!</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="user-avatar me-3">
                <!-- <?php echo strtoupper(substr($_SESSION['a_info']['afname'], 0, 1)); ?> -->
            </div>
            <div>
                <span class="badge bg-success">Online</span>
            </div>
        </div>
    </div>

    <div class="recent-activity mt-4">
        <h5 class="mb-4">Change Password</h5>
        <div class="table-responsive">
            <div class="col-6">
                <form name="chg-frm" id="chg-frm" method="post">
                    <div class="mb-3">
                        <label for="pwd" class="form-label">Enter current password</label> <span id="invalid-pwd" class="text-danger"></span>
                        <input type="password" class="form-control" id="pwd" name="pwd">
                    </div>
                    <div class="mb-3">
                        <label for="npwd" class="form-label">Enter new password</label> <span id="invalid-npwd" class="text-danger"></span>
                        <input type="password" class="form-control" id="npwd" name="npwd">
                    </div>
                    <div class="mb-3">
                        <label for="cpwd" class="form-label">Enter confirm password</label> <span id="invalid-cpwd" class="text-danger"></span>
                        <input type="password" class="form-control" id="cpwd" name="cpwd">
                    </div>
                    <input type="submit" name="ok" class="btn btn-primary" value="Save Changes">
                </form>
                <?php
                if (isset($_POST['ok'])) {
                    $pwd = $_POST['pwd'];
                    $npwd = $_POST['npwd'];
                    $cpwd = $_POST['cpwd'];
                    try {
                        if (password_verify($pwd, $row['pwd'])) {
                            if ($npwd == $cpwd) {
                                if (password_verify($npwd, $row['pwd'])) {
                                    $error = "New password can not same as your current password";
                                } else {
                                    $npwd = password_hash($npwd, PASSWORD_DEFAULT);
                                    $upd = "UPDATE user SET pwd='$npwd' WHERE email_id='$email_id'";
                                    $con->query($upd);
                                    $msg = 'Your password has been changed successfully';
                                }
                            } else {
                                $error = "Your confirm password does not match";
                            }
                        } else {
                            $error = "Your current password does not match";
                        }
                    } catch (Exception $e) {
                        echo "Error: " . $e->getMessage();
                    }
                }
                echo $error;
                echo $msg;
                ?>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="asstees/js/jquery.validate.js"></script>
    <script>
        $(document).ready(function() {
            jQuery.validator.setDefaults({
                errorPlacement: function(error, element) {
                    error.appendTo('#invalid-' + element.attr('id'));
                }
            });
            $("#chg-frm").validate({
                rules: {
                    pwd: {
                        required: true,
                    },
                    npwd: {
                        required: true
                    },
                    cpwd: {
                        required: true
                    }
                },
                messages: {
                    pwd: {
                        required: 'Please enter your Password'
                    },
                    npwd: {
                        required: 'Please enter your New Password'
                    },
                    cpwd: {
                        required: 'Please enter your Confirm Password'
                    },
                    highlight: function(element) {
                        $(element).addClass("is-invalid");
                    },

                    unhighlight: function(element) {
                        $(element).removeClass("is-invalid");
                    }
                }
            });
        });
    </script>

    <?php require('include/footer.php') ?>