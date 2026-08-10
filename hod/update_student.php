<?php
require_once('config.php');
require('include/header.php');

if (!isset($_SESSION['h_info']['email_id'])) {
    header("Location: login.php");
    exit();
}

// Optional: Ensure semester column exists in student table
// Uncomment the following block if you need to add the column automatically
/*
$check_column = $con->query("SHOW COLUMNS FROM student LIKE 'semester'");
if ($check_column->num_rows == 0) {
    $con->query("ALTER TABLE student ADD COLUMN semester VARCHAR(20) DEFAULT NULL");
}
*/

$error = '';
$success = '';
$student = null;

// Get student ID from URL
if (!isset($_GET['sid']) || empty($_GET['sid'])) {
    header("Location: student_management.php?search=1");
    exit();
}
$sid = intval($_GET['sid']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
    $fname       = trim($con->real_escape_string($_POST['fname']));
    $mname       = trim($con->real_escape_string($_POST['mname']));
    $lname       = trim($con->real_escape_string($_POST['lname']));
    $rollno      = trim($con->real_escape_string($_POST['rollno']));
    $fathername  = trim($con->real_escape_string($_POST['fathername']));
    $mothername  = trim($con->real_escape_string($_POST['mothername']));
    $mobile      = trim($con->real_escape_string($_POST['mobile']));
    $email_id    = trim($con->real_escape_string($_POST['email_id']));
    $gender      = trim($con->real_escape_string($_POST['gender']));
    $semester    = trim($con->real_escape_string($_POST['semester']));

    if (empty($fname) || empty($lname) || empty($rollno) || empty($fathername) || empty($mothername) || empty($mobile) || empty($email_id) || empty($gender)) {
        $error = "Please fill all required fields (First Name, Last Name, Roll No, Father Name, Mother Name, Mobile, Email, Gender).";
    } elseif (!filter_var($email_id, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $error = "Mobile number must be exactly 10 digits.";
    } else {
        $sql = "UPDATE student SET 
                    fname = '$fname',
                    mname = '$mname',
                    lname = '$lname',
                    rollno = '$rollno',
                    fathername = '$fathername',
                    mothername = '$mothername',
                    mobile = '$mobile',
                    email_id = '$email_id',
                    gender = '$gender',
                    semester = '$semester'
                WHERE sid = $sid";

        if ($con->query($sql) === TRUE) {
            $success = "Student record updated successfully!";
            $result = $con->query("SELECT * FROM student WHERE sid = $sid");
            if ($result && $result->num_rows > 0) {
                $student = $result->fetch_assoc();
            }
        } else {
            $error = "Database error: " . $con->error;
        }
    }
}

// Fetch current student data
if ($student === null) {
    $result = $con->query("SELECT * FROM student WHERE sid = $sid");
    if ($result && $result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        header("Location: student_management.php?search=1");
        exit();
    }
}
?>

<style>
    .main-content {
        padding: 2rem;
        background: #f8f9fc;
        min-height: 100vh;
    }

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
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .form-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #4e73df;
        margin-bottom: 0.5rem;
    }

    .form-control,
    .form-select {
        border-radius: 0.5rem;
        border: 1px solid #d1d3e2;
        padding: 0.6rem 1rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4e73df, #224abe);
        border: none;
        border-radius: 0.5rem;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #224abe, #1a3a8f);
    }

    .btn-secondary {
        background: #858796;
        border: none;
        border-radius: 0.5rem;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        color: white;
        text-decoration: none;
    }

    .alert {
        border-radius: 0.75rem;
        border-left: 4px solid;
        margin-bottom: 1rem;
    }

    .alert-success {
        border-left-color: #1cc88a;
        background-color: #e3f7ec;
    }

    .alert-danger {
        border-left-color: #e74a3b;
        background-color: #fbe9e7;
    }

    @media (max-width: 768px) {
        .main-content {
            padding: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
    }
</style>

<div class="main-content">
    
    <div class="header">
        <h2>Update Student</h2>
        <div class="user-info">
            <div class="user-avatar">
                <p><?php echo substr($_SESSION['h_info']['tfname'], 0, 1) . substr($_SESSION['h_info']['tmname'], 0, 1). substr($_SESSION['h_info']['tlname'], 0, 1) ?></p>
            </div>
            <div>
                <p>Welcome back, <?php echo $_SESSION['h_info']['tfname'] . " " . $_SESSION['h_info']['tmname']. " " . $_SESSION['h_info']['tlname']; ?>!</p>
            </div>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="form-card">
        <form method="post" action="">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="fname" class="form-label">First Name </label>
                    <input type="text" class="form-control" id="fname" name="fname" value="<?php echo htmlspecialchars($student['fname']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="mname" class="form-label">Middle Name</label>
                    <input type="text" class="form-control" id="mname" name="mname" value="<?php echo htmlspecialchars($student['mname']); ?>">
                </div>
                <div class="col-md-4">
                    <label for="lname" class="form-label">Last Name </label>
                    <input type="text" class="form-control" id="lname" name="lname" value="<?php echo htmlspecialchars($student['lname']); ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="rollno" class="form-label">Roll Number </label>
                    <input type="text" readonly class="form-control" id="rollno" name="rollno" value="<?php echo htmlspecialchars($student['rollno']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="gender" class="form-label">Gender </label>
                    <select class="form-select" id="gender" name="gender" required>
                        <option value="">Select</option>
                        <option value="Male" <?php echo ($student['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($student['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($student['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="fathername" class="form-label">Father's Name </label>
                    <input type="text" class="form-control" id="fathername" name="fathername" value="<?php echo htmlspecialchars($student['fathername']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="mothername" class="form-label">Mother's Name</label>
                    <input type="text" class="form-control" id="mothername" name="mothername" value="<?php echo htmlspecialchars($student['mothername']); ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="mobile" class="form-label">Mobile Number  (10 digits)</label>
                    <input type="tel" class="form-control" id="mobile" name="mobile" value="<?php echo htmlspecialchars($student['mobile']); ?>" pattern="[0-9]{10}" required>
                </div>
                <div class="col-md-6">
                    <label for="email_id" class="form-label">Email Address </label>
                    <input type="email" class="form-control" id="email_id" name="email_id" value="<?php echo htmlspecialchars($student['email_id']); ?>" required>
                </div>

                <!-- New Semester Field -->
                <div class="col-md-6">
                    <label for="semester" class="form-label">Semester</label>
                    <input type="text" readonly class="form-control" id="semester" name="semester" value="<?php echo htmlspecialchars($student['semester'] ?? ''); ?>">
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" name="update_student" class="btn btn-primary">Update Student</button>
                    <a href="updstd.php" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
    <?php require('include/footer.php'); ?>
</div>