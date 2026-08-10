<?php
require_once('config.php');
require('include/header.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

// ========== HANDLE PROFILE UPDATE ==========
$update_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Retrieve session teacher ID
    $tid = $_SESSION['h_info']['tid'] ?? 0;
    if ($tid) {
        // Sanitize inputs
        $tfname   = trim($_POST['tfname']);
        $tmname   = trim($_POST['tmname']);
        $tlname   = trim($_POST['tlname']);
        $tmobile  = trim($_POST['tmobile']);
        $hire_date = trim($_POST['hire_date']);
        $email_id = trim($_POST['email_id']);
        $taddress = trim($_POST['taddress']);

        // Basic validation
        if (empty($tfname) || empty($tlname) || empty($email_id)) {
            $update_message = "First name, last name, and email are required.";
        } else {
            // Update database
            $update_sql = "UPDATE teacher SET 
                            tfname = ?, tmname = ?, tlname = ?, 
                            tmobile = ?, hire_date = ?, email_id = ?, taddress = ? 
                           WHERE tid = ?";
            $stmt = $con->prepare($update_sql);
            $stmt->bind_param("sssssssi", $tfname, $tmname, $tlname, $tmobile, $hire_date, $email_id, $taddress, $tid);
            if ($stmt->execute()) {
                // Update session data
                $_SESSION['h_info']['tfname'] = $tfname;
                $_SESSION['h_info']['tmname'] = $tmname;
                $_SESSION['h_info']['tlname'] = $tlname;
                $_SESSION['h_info']['tmobile'] = $tmobile;
                $_SESSION['h_info']['hire_date'] = $hire_date;
                $_SESSION['h_info']['email_id'] = $email_id;
                $_SESSION['h_info']['taddress'] = $taddress;
                $update_message = "Profile updated successfully!";
                // Refresh page to show updated data
                echo "<meta http-equiv='refresh' content='0'>";
                exit;
            } else {
                $update_message = "Database error: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $update_message = "Session error: teacher ID not found.";
    }
}

// Fetch teacher details from session (assumes they are already stored in $_SESSION['h_info'])
$teacher = $_SESSION['h_info'] ?? null;

if (!$teacher) {
    // If session data is missing, redirect to login
    header('Location: login.php');
    exit;
}

// Extract individual fields with defaults
$tfname   = htmlspecialchars($teacher['tfname'] ?? '');
$tmname   = htmlspecialchars($teacher['tmname'] ?? '');
$tlname   = htmlspecialchars($teacher['tlname'] ?? '');
$tmobile  = htmlspecialchars($teacher['tmobile'] ?? '');
$hire_date = htmlspecialchars($teacher['hire_date'] ?? '');
$email_id = htmlspecialchars($teacher['email_id'] ?? '');
$taddress = htmlspecialchars($teacher['taddress'] ?? '');
$full_name = trim("$tfname $tmname $tlname");
?>

<!-- ADDITIONAL CSS STYLES -->
<style>
    /* Profile card container */
    .student-list-container {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 25px 30px;
        margin: 20px 0;
        transition: box-shadow 0.3s ease;
    }
    .student-list-container:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    /* Section header */
    .section-header h3 {
        font-size: 1.6rem;
        font-weight: 600;
        color: #2c3e66;
        margin: 0;
    }

    /* Table styling */
    .student-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.95rem;
        margin-top: 20px;
        border-radius: 10px;
        overflow: hidden;
    }
    .student-table th,
    .student-table td {
        padding: 14px 16px;
        text-align: left;
        border-bottom: 1px solid #e9ecef;
    }
    .student-table th {
        background-color: #f8f9fc;
        color: #1f2b48;
        font-weight: 600;
        width: 200px;
        border-right: 1px solid #e9ecef;
    }
    .student-table td {
        background-color: #ffffff;
        color: #344767;
    }
    .student-table tr:hover td {
        background-color: #f1f5f9;
    }

    /* Alert messages */
    .alert {
        padding: 12px 20px;
        margin: 20px 0;
        border-radius: 8px;
        font-size: 0.9rem;
        border-left: 4px solid;
    }
    .alert-success {
        background-color: #e6f7e6;
        border-left-color: #2e7d32;
        color: #1e4620;
    }
    .alert-error {
        background-color: #ffe6e6;
        border-left-color: #c62828;
        color: #8b1e1e;
    }

    /* Update form container */
    #updateFormContainer {
        background: #fefefe;
        border-radius: 12px;
        padding: 20px 25px;
        margin-top: 30px;
        border-top: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    #updateFormContainer h3 {
        font-size: 1.4rem;
        margin-bottom: 20px;
        color: #2c3e66;
        font-weight: 500;
        border-left: 4px solid #4CAF50;
        padding-left: 15px;
    }

    /* Form grid */
    #updateFormContainer form > div {
        display: grid;
        grid-template-columns: 160px 1fr;
        gap: 16px 20px;
        align-items: center;
    }
    #updateFormContainer label {
        font-weight: 500;
        color: #2c3e66;
        justify-self: end;
    }
    #updateFormContainer input,
    #updateFormContainer select,
    #updateFormContainer textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #cfdde6;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: all 0.2s;
        background-color: #fff;
    }
    #updateFormContainer input:focus,
    #updateFormContainer select:focus,
    #updateFormContainer textarea:focus {
        outline: none;
        border-color: #4CAF50;
        box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
    }
    #updateFormContainer input[readonly] {
        background-color: #f5f7fa;
        color: #6c757d;
        cursor: not-allowed;
    }

    /* Buttons */
    .btn-filter, .btn-clear, #showUpdateFormBtn {
        padding: 8px 20px;
        border: none;
        border-radius: 30px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.85rem;
    }
    .btn-filter {
        background-color: #4CAF50;
        color: white;
    }
    .btn-filter:hover {
        background-color: #45a049;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(76, 175, 80, 0.2);
    }
    .btn-clear {
        background-color: #f0f2f5;
        color: #5a6e8a;
        border: 1px solid #cfdde6;
    }
    .btn-clear:hover {
        background-color: #e4e7ed;
    }
    #showUpdateFormBtn {
        background-color: #4CAF50;
        color: white;
        font-size: 0.9rem;
        padding: 8px 22px;
    }
    #showUpdateFormBtn:hover {
        background-color: #43a047;
        transform: scale(1.02);
    }
    .form-buttons {
        grid-column: 2 / 3;
        text-align: right;
        margin-top: 10px;
    }
    .form-buttons button {
        margin-left: 12px;
        padding: 8px 24px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .student-list-container {
            padding: 15px;
        }
        .student-table th,
        .student-table td {
            padding: 10px 12px;
            display: block;
            width: 100%;
        }
        .student-table th {
            border-right: none;
            background-color: #eef2f7;
        }
        .student-table tr {
            margin-bottom: 15px;
            display: block;
            border: 1px solid #e9ecef;
            border-radius: 10px;
        }
        #updateFormContainer form > div {
            grid-template-columns: 1fr;
            gap: 8px;
        }
        #updateFormContainer label {
            justify-self: start;
            margin-top: 10px;
        }
        .form-buttons {
            grid-column: 1;
            text-align: center;
        }
        .section-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
    }
</style>

<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="header">
        <h2>My Profile</h2>
        <div class="user-info">
            <div class="user-avatar">
                <p><?php echo substr($tfname, 0, 1) . substr($tmname, 0, 1) . substr($tlname, 0, 1); ?></p>
            </div>
            <div>
                <p>Welcome back, <?php echo $full_name; ?>!</p>
            </div>
        </div>
    </div>

    <!-- Profile Details Card -->
    <div class="student-list-container">
        <div class="section-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3>Teacher Information</h3>
            <button id="showUpdateFormBtn" class="btn-filter" style="background-color: #4CAF50;">Update Profile</button>
        </div>

        <?php if (!empty($update_message)): ?>
            <div class="alert <?php echo strpos($update_message, 'successfully') !== false ? 'alert-success' : 'alert-error'; ?>">
                <?php echo htmlspecialchars($update_message); ?>
            </div>
        <?php endif; ?>

        <!-- Display Table -->
        <table class="student-table" style="width: 100%;">
            <tbody>
                <tr><th style="width: 200px;">Full Name</th><td><?php echo $full_name; ?></td></tr>
                <tr><th>First Name</th><td><?php echo $tfname; ?></td></tr>
                <tr><th>Middle Name</th><td><?php echo $tmname; ?></td></tr>
                <tr><th>Last Name</th><td><?php echo $tlname; ?></td></tr>
                <tr><th>Phone Number</th><td><?php echo $tmobile; ?></td></tr>
                <tr><th>Hire Date</th><td><?php echo $hire_date; ?></td></tr>
                <tr><th>Email ID</th><td><?php echo $email_id; ?></td></tr>
                <tr><th>Address</th><td><?php echo nl2br($taddress); ?></td></tr>
            </tbody>
        </table>

        <!-- Hidden Update Form (shown when button clicked) -->
        <div id="updateFormContainer" style="display: none;">
            <h3>Edit Profile</h3>
            <form method="post" action="">
                <div>
                    <label for="tfname">First Name </label>
                    <input type="text" readonly name="tfname" id="tfname" value="<?php echo $tfname; ?>" required>

                    <label for="tmname">Middle Name</label>
                    <input type="text" readonly name="tmname" id="tmname" value="<?php echo $tmname; ?>">

                    <label for="tlname">Last Name </label>
                    <input type="text" readonly name="tlname" id="tlname" value="<?php echo $tlname; ?>" required>

                    <label for="tmobile">Phone Number</label>
                    <input type="text" name="tmobile" id="tmobile" value="<?php echo $tmobile; ?>">

                    <label for="hire_date">Hire Date</label>
                    <input type="date" name="hire_date" id="hire_date" value="<?php echo $hire_date; ?>">

                    <label for="email_id">Email ID </label>
                    <input type="email" readonly name="email_id" id="email_id" value="<?php echo $email_id; ?>" required>

                    <label for="taddress">Address</label>
                    <textarea name="taddress" id="taddress" rows="3"><?php echo $taddress; ?></textarea>

                    <div class="form-buttons">
                        <button type="button" id="cancelUpdateBtn" class="btn-clear">Cancel</button>
                        <button type="submit" name="update_profile" class="btn-filter">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toggle update form visibility
        const showBtn = document.getElementById('showUpdateFormBtn');
        const formContainer = document.getElementById('updateFormContainer');
        const cancelBtn = document.getElementById('cancelUpdateBtn');

        showBtn.addEventListener('click', () => {
            formContainer.style.display = 'block';
            showBtn.style.display = 'none';
        });

        cancelBtn.addEventListener('click', () => {
            formContainer.style.display = 'none';
            showBtn.style.display = 'inline-block';
        });
    </script>

    <?php
    require('include/footer.php');
    ?>
</body>
</html>