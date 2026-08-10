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
    // Retrieve session student ID
    $sid = $_SESSION['s_info']['sid'] ?? 0;
    if ($sid) {
        // Sanitize inputs
        $fname      = trim($_POST['fname']);
        $mname      = trim($_POST['mname']);
        $lname      = trim($_POST['lname']);
        $rollno     = trim($_POST['rollno']);
        $fathername = trim($_POST['fathername']);
        $mothername = trim($_POST['mothername']);
        $mobile     = trim($_POST['mobile']);
        $email_id   = trim($_POST['email_id']);
        $gender     = trim($_POST['gender']);
        $semester   = trim($_POST['semester']);

        // Basic validation
        if (empty($fname) || empty($lname) || empty($rollno) || empty($email_id)) {
            $update_message = "First name, last name, roll number, and email are required.";
        } else {
            // Update database
            $update_sql = "UPDATE student SET 
                            fname = ?, mname = ?, lname = ?, rollno = ?,
                            fathername = ?, mothername = ?, mobile = ?,
                            email_id = ?, gender = ?, semester = ?
                           WHERE sid = ?";
            $stmt = $con->prepare($update_sql);
            $stmt->bind_param("ssssssssssi", 
                $fname, $mname, $lname, $rollno,
                $fathername, $mothername, $mobile,
                $email_id, $gender, $semester, $sid
            );
            if ($stmt->execute()) {
                // Update session data
                $_SESSION['s_info']['fname']      = $fname;
                $_SESSION['s_info']['mname']      = $mname;
                $_SESSION['s_info']['lname']      = $lname;
                $_SESSION['s_info']['rollno']     = $rollno;
                $_SESSION['s_info']['fathername'] = $fathername;
                $_SESSION['s_info']['mothername'] = $mothername;
                $_SESSION['s_info']['mobile']     = $mobile;
                $_SESSION['s_info']['email_id']   = $email_id;
                $_SESSION['s_info']['gender']     = $gender;
                $_SESSION['s_info']['semester']   = $semester;
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
        $update_message = "Session error: student ID not found.";
    }
}

// Fetch student details from session (assumes they are stored in $_SESSION['s_info'])
$student = $_SESSION['s_info'] ?? null;

if (!$student) {
    // If session data is missing, redirect to login
    header('Location: login.php');
    exit;
}

// Extract individual fields with defaults
$fname      = htmlspecialchars($student['fname'] ?? '');
$mname      = htmlspecialchars($student['mname'] ?? '');
$lname      = htmlspecialchars($student['lname'] ?? '');
$rollno     = htmlspecialchars($student['rollno'] ?? '');
$fathername = htmlspecialchars($student['fathername'] ?? '');
$mothername = htmlspecialchars($student['mothername'] ?? '');
$mobile     = htmlspecialchars($student['mobile'] ?? '');
$email_id   = htmlspecialchars($student['email_id'] ?? '');
$gender     = htmlspecialchars($student['gender'] ?? '');
$semester   = htmlspecialchars($student['semester'] ?? '');
$full_name  = trim("$fname $mname $lname");
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
    <!-- Profile Details Card -->
    <div class="student-list-container">
        <div class="section-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3>Student Information</h3>
            <button id="showUpdateFormBtn" class="btn-filter" style="background-color: #4CAF50;">Update Profile</button>
        </div>

        <?php if (!empty($update_message)): ?>
            <div class="alert <?php echo strpos($update_message, 'successfully') !== false ? 'alert-success' : 'alert-error'; ?>">
                <?php echo htmlspecialchars($update_message); ?>
            </div>
        <?php endif; ?>

        <!-- Display Table -->
        <table class="student-table">
            <tbody>
                <tr><th>Full Name</th><td><?php echo $full_name; ?></td></tr>
                <tr><th>First Name</th><td><?php echo $fname; ?></td></tr>
                <tr><th>Middle Name</th><td><?php echo $mname; ?></td></tr>
                <tr><th>Last Name</th><td><?php echo $lname; ?></td></tr>
                <tr><th>Roll Number</th><td><?php echo $rollno; ?></td></tr>
                <tr><th>Father's Name</th><td><?php echo $fathername; ?></td></tr>
                <tr><th>Mother's Name</th><td><?php echo $mothername; ?></td></tr>
                <tr><th>Phone Number</th><td><?php echo $mobile; ?></td></tr>
                <tr><th>Email ID</th><td><?php echo $email_id; ?></td></tr>
                <tr><th>Gender</th><td><?php echo $gender; ?></td></tr>
                <tr><th>Semester</th><td><?php echo $semester; ?></td></tr>
            </tbody>
        </table>

        <!-- Hidden Update Form (shown when button clicked) -->
        <div id="updateFormContainer" style="display: none;">
            <h3>Edit Profile</h3>
            <form method="post" action="">
                <div>
                    <label for="fname">First Name </label>
                    <input type="text" readonly name="fname" id="fname" value="<?php echo $fname; ?>" required>

                    <label for="mname">Middle Name</label>
                    <input type="text" readonly name="mname" id="mname" value="<?php echo $mname; ?>">

                    <label for="lname">Last Name </label>
                    <input type="text" readonly name="lname" id="lname" value="<?php echo $lname; ?>" required>

                    <label for="rollno">Roll Number </label>
                    <input type="text" readonly name="rollno" id="rollno" value="<?php echo $rollno; ?>" required>

                    <label for="fathername">Father's Name</label>
                    <input type="text" name="fathername" id="fathername" value="<?php echo $fathername; ?>">

                    <label for="mothername">Mother's Name</label>
                    <input type="text" name="mothername" id="mothername" value="<?php echo $mothername; ?>">

                    <label for="mobile">Phone Number</label>
                    <input type="text" name="mobile" id="mobile" value="<?php echo $mobile; ?>">

                    <label for="email_id">Email ID </label>
                    <input type="email" readonly name="email_id" id="email_id" value="<?php echo $email_id; ?>" required>

                    <label for="gender">Gender</label>
                    <select name="gender" id="gender">
                        <option value="Male" <?php echo $gender == 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $gender == 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo $gender == 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>

                    <label for="semester">Semester</label>
                    <input type="number" readonly name="semester" id="semester" value="<?php echo $semester; ?>" min="1" max="8">

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