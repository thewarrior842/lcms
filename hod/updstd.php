<?php
require('include/header.php');
require_once('config.php');
if (!isset($_SESSION['h_info']['email_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables for filter
$filtered_students = [];
$search_name = "";
$search_rollno = "";
$search_semester = "";

// Handle filter/search
if (isset($_GET['search'])) {
    $search_name = trim($_GET['name']);
    $search_rollno = trim($_GET['rollno']);
    $search_semester = trim($_GET['semester']);
    
    $sql = "SELECT * FROM student WHERE 1=1";
    if (!empty($search_name)) {
        $search_name = $con->real_escape_string($search_name);
        $sql .= " AND (fname LIKE '%$search_name%' OR mname LIKE '%$search_name%' OR lname LIKE '%$search_name%')";
    }
    if (!empty($search_rollno)) {
        $search_rollno = $con->real_escape_string($search_rollno);
        $sql .= " AND rollno LIKE '%$search_rollno%'";
    }
    if (!empty($search_semester)) {
        $search_semester = $con->real_escape_string($search_semester);
        $sql .= " AND semester LIKE '%$search_semester%'";
    }
    $sql .= " ORDER BY rollno";
    $result = $con->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($stud = $result->fetch_assoc()) {
            $filtered_students[] = $stud;
        }
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
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    .user-avatar:hover {
        transform: scale(1.05);
    }
    .filter-card {
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
    .form-control {
        border-radius: 0.5rem;
        border: 1px solid #d1d3e2;
        padding: 0.6rem 1rem;
        transition: all 0.2s;
    }
    .form-control:focus {
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
    .btn-secondary {
        background: #858796;
        border: none;
        border-radius: 0.5rem;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        color: white;
    }
    .btn-secondary:hover {
        background: #6e707e;
    }
    .btn-sm {
        padding: 0.25rem 0.8rem;
        font-size: 0.875rem;
    }
    .alert {
        border-radius: 0.75rem;
        border-left: 4px solid;
    }
    .alert-info {
        border-left-color: #36b9cc;
        background-color: #e6f7fa;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .table {
        width: 100%;
        background: white;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .table th {
        background: #4e73df;
        color: white;
        border: none;
        padding: 0.75rem;
    }
    .table td {
        padding: 0.75rem;
        vertical-align: middle;
    }
    @media (max-width: 768px) {
        .main-content { padding: 1rem; }
        .user-avatar { width: 40px; height: 40px; font-size: 1rem; }
    }
    @media (max-width: 576px) {
        .header-container { flex-direction: column !important; align-items: flex-start !important; }
        .right-items { margin-left: 0 !important; margin-top: 0.5rem; }
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

    <div class="filter-card">
        <h5 class="mb-3">Filter Students</h5>
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label for="name" class="form-label">Name (First or Last)</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter name" value="<?php echo htmlspecialchars($search_name); ?>">
            </div>
            <div class="col-md-4">
                <label for="rollno" class="form-label">Roll Number</label>
                <input type="text" class="form-control" id="rollno" name="rollno" placeholder="Enter roll number" value="<?php echo htmlspecialchars($search_rollno); ?>">
            </div>
           
            <div class="col-md-12 d-flex align-items-end gap-2">
                <button type="submit" name="search" class="btn btn-primary">Search</button>
                <a href="?" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <?php if (isset($_GET['search']) && !empty($filtered_students)): ?>
    <div class="filter-card">
        <h5 class="mb-3">Search Results (<?php echo count($filtered_students); ?> found)</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Roll No</th>
                        <th>Father Name</th>
                        <th>Mother Name</th>
                        <th>Phone Number</th>
                        <th>Email</th>
                        <th>Gender</th>
                        <th>Semester</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filtered_students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['sid']); ?></td>
                        <td><?php echo htmlspecialchars($student['fname']); ?></td>
                        <td><?php echo htmlspecialchars($student['mname']); ?></td>
                        <td><?php echo htmlspecialchars($student['lname']); ?></td>
                        <td><?php echo htmlspecialchars($student['rollno']); ?></td>
                        <td><?php echo htmlspecialchars($student['fathername']); ?></td>
                        <td><?php echo htmlspecialchars($student['mothername']); ?></td>
                        <td><?php echo htmlspecialchars($student['mobile']); ?></td>
                        <td><?php echo htmlspecialchars($student['email_id']); ?></td>
                        <td><?php echo htmlspecialchars($student['gender']); ?></td>
                        <td><?php echo htmlspecialchars($student['semester']); ?></td>
                        <td>
                            <a href="update_student.php?sid=<?php echo urlencode($student['sid']); ?>" class="btn btn-primary btn-sm">Update</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php elseif (isset($_GET['search'])): ?>
    <div class="filter-card">
        <div class="alert alert-info mb-0">No students found matching the criteria.</div>
    </div>
    <?php endif; ?>

    <?php require('include/footer.php'); ?>
</div>