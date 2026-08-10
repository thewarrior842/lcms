<?php
require('include/header.php');
require_once('config.php');
if (!isset($_SESSION['s_info']['email_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$error = "";
$msg = "";
$row = null;

try {
    $email_id = $_SESSION['s_info']['email_id'];
    $src = "SELECT pwd FROM student WHERE email_id='$email_id'";
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
                    $upd = "UPDATE student SET pwd='$npwd' WHERE email_id='$email_id'";
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

<!-- Additional Custom CSS -->
<style>
    /* General main content spacing */
    .main-content {
        padding: 2rem;
        background: #f8f9fc;
        min-height: 100vh;
    }

    /* User avatar style */
    .user-avatar {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #4e73df, #224abe);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.2rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    .user-avatar:hover {
        transform: scale(1.05);
    }

    /* Recent activity card style */
    .recent-activity {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    }

    /* Form styling */
    .recent-activity .form-label {
        font-weight: 600;
        color: #4e73df;
        margin-bottom: 0.5rem;
    }
    .recent-activity .form-control {
        border-radius: 0.5rem;
        border: 1px solid #d1d3e2;
        padding: 0.6rem 1rem;
        transition: all 0.2s;
    }
    .recent-activity .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    .btn-primary {
        background: linear-gradient(135deg, #4e73df, #224abe);
        border: none;
        border-radius: 0.5rem;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #224abe, #1a3a8f);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .alert {
        border-radius: 0.75rem;
        border-left: 4px solid;
    }
    .alert-danger {
        border-left-color: #e74a3b;
        background-color: #fef1ef;
    }
    .alert-success {
        border-left-color: #1cc88a;
        background-color: #eafaf1;
    }

    /* Responsive tweaks */
    @media (max-width: 768px) {
        .main-content {
            padding: 1rem;
        }
        .recent-activity {
            padding: 1rem;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
    }
    
    /* For small screens, stack header vertically */
    @media (max-width: 576px) {
        .header-container {
            flex-direction: column !important;
            align-items: flex-start !important;
        }
        .right-items {
            margin-left: 0 !important;
            margin-top: 0.5rem;
        }
    }
</style>

<!-- Main Content -->
<div class="main-content">
    <!-- Header: title on left, avatar + badge on right -->
    <div class="d-flex flex-wrap align-items-center header-container">
        <!-- Left side: title and welcome message -->
        <div>
            <h2 class="mb-1">Change Password</h2>
            
        </div>
        <!-- Right side: avatar and online badge - using ms-auto to push to the right -->
        <div class="d-flex align-items-center gap-3 ms-auto right-items">
            
        </div>
    </div>

    <div class="recent-activity mt-4">
        
        <div class="row">
            <div class="col-12 col-md-6">
                <form name="frm" method="post">
                    <div class="mb-3">
                        <label for="pwd" class="form-label">Enter current password</label>
                        <input type="password" class="form-control" id="pwd" name="pwd" required> 
                    </div><br>
                    <div class="mb-3">
                        <label for="npwd" class="form-label">Enter new password</label>
                        <input type="password" class="form-control" id="npwd" name="npwd" required>
                    </div><br>
                    <div class="mb-3">
                        <label for="cpwd" class="form-label">Enter confirm password</label>
                        <input type="password" class="form-control" id="cpwd" name="cpwd" required>
                    </div>
                    <input type="submit" name="ok" class="btn btn-white" value="Save Changes">
                </form>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($msg): ?>
                    <div class="alert alert-success mt-3"><?php echo htmlspecialchars($msg); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php require('include/footer.php'); ?>
</div>