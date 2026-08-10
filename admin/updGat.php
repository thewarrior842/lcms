<?php
require('include/header.php');
require_once('config.php');

// Redirect if no gatekeeper ID is provided
if (empty($_POST['gid'])) {
    header('location:gatekeeper.php');
    exit;
}

try {
    $gid = $_POST['gid'];
    $src = "SELECT * FROM gatekeeper WHERE gid=$gid";
    $result = $con->query($src);
    $row = $result->fetch_assoc();
} catch (mysqli_sql_exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Gatekeepers</h2>
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
        <h5 class="mb-4">Update Gatekeeper</h5>
        <div class="table-responsive">
            <form class="row g-3" name="frm" method="post">
                <div class="col-md-6">
                    <label for="gfname" class="form-label">Enter New Gatekeeper First Name</label>
                    <input type="text" class="form-control" id="gfname" name="gfname" value="<?php echo htmlspecialchars($row['gfname']); ?>">
                </div>
                <div class="col-md-6">
                    <label for="glname" class="form-label">Enter New Gatekeeper Last Name</label>
                    <input type="text" class="form-control" id="glname" name="glname" value="<?php echo htmlspecialchars($row['glname']); ?>">
                </div>
                <div class="col-md-6">
                    <label for="email_id" class="form-label">Enter New Gatekeeper Email</label>
                    <input type="email" class="form-control" id="email_id" name="email_id" value="<?php echo htmlspecialchars($row['email_id']); ?>">
                </div>
                <div class="col-md-6">
                    <label for="gmobile" class="form-label">Enter New Gatekeeper Mobile</label>
                    <input type="number" class="form-control" id="gmobile" name="gmobile" value="<?php echo htmlspecialchars($row['gmobile']); ?>">
                </div>
                <div class="col-md-6">
                    <label for="hire_date" class="form-label">Enter New Gatekeeper Hire Date</label>
                    <input type="date" class="form-control" id="hire_date" name="hire_date" value="<?php echo htmlspecialchars($row['hire_date']); ?>">
                </div>
                <div class="col-12">
                    <label for="gaddress" class="form-label">Enter New Gatekeeper Address</label>
                    <input type="text" class="form-control" id="gaddress" name="gaddress" value="<?php echo htmlspecialchars($row['gaddress']); ?>">
                </div>
                <input type="hidden" name="gid" value="<?php echo htmlspecialchars($row['gid']); ?>">
                <input type="submit" name="ok" class="btn btn-primary" value="Save Changes">
            </form>

            <?php
            // Handle form submission
            if (isset($_POST['ok'])) {
                try {
                    // Get form data
                    $gfname    = $_POST['gfname'];
                    $glname    = $_POST['glname'];
                    $email_id  = $_POST['email_id'];
                    $gmobile   = $_POST['gmobile'];
                    $hire_date = $_POST['hire_date'];
                    $gaddress  = $_POST['gaddress'];
                    $gid       = $_POST['gid'];

                    // 1. Retrieve current email from gatekeeper table before update
                    $oldEmailQuery = "SELECT email_id FROM gatekeeper WHERE gid = $gid";
                    $oldEmailResult = $con->query($oldEmailQuery);
                    $oldEmailRow = $oldEmailResult->fetch_assoc();
                    $oldEmail = $oldEmailRow['email_id'];

                    // 2. Update gatekeeper table with new data
                    $updGatekeeper = "UPDATE gatekeeper SET 
                                        gfname='$gfname', 
                                        glname='$glname', 
                                        email_id='$email_id', 
                                        gmobile='$gmobile', 
                                        hire_date='$hire_date', 
                                        gaddress='$gaddress' 
                                      WHERE gid=$gid";
                    $con->query($updGatekeeper);

                    // 3. If email has changed, update the user table as well
                    if ($oldEmail != $email_id) {
                        $updUser = "UPDATE user SET email_id='$email_id' WHERE email_id='$oldEmail'";
                        $con->query($updUser);
                    }

                    // Redirect with success message
                    echo "<script>window.location='gatekeeper.php?msg=Gatekeeper details updated successfully';</script>";
                    exit;

                } catch (mysqli_sql_exception $e) {
                    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
                }
            }
            ?>
        </div>
    </div>

    <?php require('include/footer.php'); ?>