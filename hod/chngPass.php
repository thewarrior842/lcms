<?php
require('include/header.php');
require_once('config.php');
if (!isset($_SESSION['h_info']['email_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$error = "";
$msg = "";
$row = null;

try {
    $email_id = $_SESSION['h_info']['email_id'];
    $src = "SELECT pwd FROM user WHERE email_id='$email_id'";
    $rs = $con->query($src);
    $row = $rs->fetch_assoc();
} catch (mysqli_sql_exception $e) {
    $error = "Error: " . $e->getMessage();
}

if (isset($_POST['ok'])) {
    $pwd = $_POST['pwd'];
    $npwd = $_POST['npwd'];
    $cpwd = $_POST['cpwd'];
    try {
        if (password_verify($pwd, $row['pwd'])) {
            if ($npwd == $cpwd) {
                if (password_verify($npwd, $row['pwd'])) {
                    $error = "New password cannot be the same as your current password";
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
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Change Password</h2>
            
        </div>
        <div class="d-flex align-items-center">
            <div class="user-avatar">
                <p><?php echo substr($_SESSION['h_info']['tfname'], 0, 1) . substr($_SESSION['h_info']['tmname'], 0, 1). substr($_SESSION['h_info']['tlname'], 0, 1) ?></p>
            </div>
            <div>
                <p>Welcome back, <?php echo $_SESSION['h_info']['tfname'] . " " . $_SESSION['h_info']['tmname']. " " . $_SESSION['h_info']['tlname']; ?>!</p>
            </div>
        </div>
    </div>

    <div class="recent-activity mt-4">
        <h5 class="mb-4">Change Password</h5>
        <div class="row">
            <div class="col-12 col-md-6">
                <form name="frm" method="post">
                    <div class="mb-3">
                        <label for="pwd" class="form-label">Enter current password</label>
                        <input type="password" class="form-control" id="pwd" name="pwd" required>
                    </div>
                    <div class="mb-3">
                        <label for="npwd" class="form-label">Enter new password</label>
                        <input type="password" class="form-control" id="npwd" name="npwd" required>
                    </div>
                    <div class="mb-3">
                        <label for="cpwd" class="form-label">Enter confirm password</label>
                        <input type="password" class="form-control" id="cpwd" name="cpwd" required>
                    </div>
                    <input type="submit" name="ok" class="btn btn-primary" value="Save Changes">
                </form>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($msg): ?>
                    <div class="alert alert-success mt-3"><?php echo htmlspecialchars($msg); ?></div>
                <?php endif; ?>
            </div>
        </div>
<?php require('include/footer.php') ?>