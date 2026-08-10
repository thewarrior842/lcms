<?php
require('include/header.php');
require_once('config.php');

// Ensure only HOD can access
if (!isset($_SESSION['h_info'])) {
    header('Location: login.php');
    exit;
}

// Get the student ID from POST (or fallback to GET)
$sid = isset($_POST['sid']) ? intval($_POST['sid']) : (isset($_GET['sid']) ? intval($_GET['sid']) : 0);
if ($sid <= 0) {
    $_SESSION['error'] = "Invalid student ID.";
    header('Location: students.php'); // Change to your main page name
    exit;
}

// Fetch student details along with department
$query = "SELECT s.*, d.dname, d.did 
          FROM student s 
          INNER JOIN dept d ON s.did = d.did 
          WHERE s.sid = ?";
$stmt = $con->prepare($query);
$stmt->bind_param('i', $sid);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    $_SESSION['error'] = "Student not found.";
    header('Location: students.php');
    exit;
}

// Fetch all entry logs for this student
$log_query = "SELECT * FROM entry_log WHERE rollno = ? ORDER BY log_date ASC, log_time ASC";
$log_stmt = $con->prepare($log_query);
$log_stmt->bind_param('s', $student['rollno']);
$log_stmt->execute();
$logs = $log_stmt->get_result();
?>

<!-- Main Content -->
<div class="main-content">
    <div class="header">
        <h2>Student Details</h2>
        <div class="user-info">
            <div class="user-avatar">
                <p><?php echo substr($_SESSION['h_info']['tfname'], 0, 1) . substr($_SESSION['h_info']['tmname'], 0, 1). substr($_SESSION['h_info']['tlname'], 0, 1) ?></p>
            </div>
            <div>
                <p>Welcome back, <?php echo $_SESSION['h_info']['tfname'] . " " . $_SESSION['h_info']['tmname']. " " . $_SESSION['h_info']['tlname']; ?>!</p>
            </div>
        </div>
    </div>

    <div class="student-detail-container">
        <!-- Back Button -->
        <div style="margin-bottom: 20px;">
            <a href="entrylog.php" class="btn-back">&larr; Back to List</a>
        </div>

        <!-- Student Profile Card -->
        <div class="profile-card">
            <h3>Student Information</h3>
            <table class="info-table" col-12>
                <tr>
                    <th>Name:</th>
                    <td><?php echo htmlspecialchars($student['fname'] . ' ' . $student['mname'] . ' ' . $student['lname']); ?></td>
                </tr>
                <tr>
                    <th>Roll No:</th>
                    <td><?php echo htmlspecialchars($student['rollno']); ?></td>
                </tr>
                <tr>
                    <th>Department:</th>
                    <td><?php echo htmlspecialchars($student['dname']); ?></td>
                </tr>
                <tr>
                    <th>Semester:</th>
                    <td><?php echo htmlspecialchars($student['semester']); ?></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><?php echo htmlspecialchars($student['email_id']); ?></td>
                </tr>
                <tr>
                    <th>Phone:</th>
                    <td><?php echo htmlspecialchars($student['mobile']); ?></td>
                </tr>

            </table>
        </div>

        <!-- Entry Logs -->
        <div class="logs-section" style="margin-top: 30px;">
            <h3>Entry Logs</h3>
            <?php if ($logs->num_rows > 0) { ?>
                <table class="student-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($log = $logs->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($log['log_date']); ?></td>
                                <td><?php echo htmlspecialchars($log['log_time']); ?></td>
                                <td>
                                    <?php if ($log['is_late'] == 1) { ?>
                                        <span class="badge late">Late</span>
                                    <?php } else { ?>
                                        <span class="badge on-time">On Time</span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No entry logs found for this student.</p>
            <?php } ?>
        </div>
    </div>
    <?php
    require('include/footer.php');
    ?>
</div>


<!-- Optional: Add some CSS for badges -->
<style>
    .badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
    }

    .badge.late {
        background-color: #f8d7da;
        color: #721c24;
    }

    .badge.on-time {
        background-color: #d4edda;
        color: #155724;
    }

    .btn-back {
        display: inline-block;
        padding: 8px 16px;
        background-color: #6c757d;
        color: white;
        text-decoration: none;
        border-radius: 4px;
    }

    .btn-back:hover {
        background-color: #5a6268;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
    }

    .info-table th,
    .info-table td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }

    .info-table th {
        background-color: #f2f2f2;
        width: 150px;
    }
</style>