<?php
require('include/header.php');
require_once('config.php');
?>
<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Teachers</h2>
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
        <h5 class="mb-4">Teachers</h5>
        <a href="newTec.php" class="btn btn-info">Add New</a>
        <a href="hisTec.php" class="btn btn-success">Delete History</a>
        <?php
        if (isset($_GET['msg'])) {
        ?>
            <div class="col-6">
                <div class="alert alert-success">
                    <?php echo $_GET['msg'] ?>
                </div>
            </div>
        <?php
        } else {
        ?>
            <div class="col-6">
                <div class="alert alert-white">
                    &nbsp;
                </div>
            </div>
        <?php
        }
        ?>

        <!-- Filter by Name -->
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="nameFilter" class="form-control" placeholder="Filter by name...">
            </div>
        </div>

        <div class="table-responsive">
            <?php
            try {
                $src = "SELECT u.log_time, u.log_date, t.*, d.dname FROM user u INNER JOIN teacher t ON u.email_id = t.email_id INNER JOIN dept d ON t.did = d.did WHERE t.status = '1'";
                $rs = $con->query($src);
                if ($rs->num_rows > 0) {
            ?>
                    <table class="table table-hover" id="teacherTable">
                        <thead>
                            <tr>
                                <th>Sl No.</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Department</th>
                                <th>Hire Date</th>
                                <th>Last Login Date</th>
                                <th>Last Login Time</th>
                                <th>Update</th>
                                <th>Delete</th>
                                <th>Can Login</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            while ($row = $rs->fetch_assoc()) {
                                // Format last login date and time, show N/A if not available
                                $lastLoginDate = !empty($row['log_date']) ? $row['log_date'] : 'N/A';
                                $lastLoginTime = !empty($row['log_time']) ? $row['log_time'] : 'N/A';
                            ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $row['tfname']; ?></td>
                                    <td><?php echo $row['tmname']; ?></td>
                                    <td><?php echo $row['tlname']; ?></td>
                                    <td><?php echo $row['email_id']; ?></td>
                                    <td><?php echo $row['tmobile']; ?></td>
                                    <td><?php echo $row['dname']; ?></td>
                                    <td><?php echo $row['hire_date']; ?></td>
                                    <td><?php echo $lastLoginDate; ?></td>
                                    <td><?php echo $lastLoginTime; ?></td>
                                    <td>
                                        <form name="upd-frm-<?php echo $i; ?>" method="post" action="updTec.php">
                                            <input type="hidden" name="tid" value="<?php echo $row['tid']; ?>">
                                            <button type="submit" class="btn text-primary"><i class="far fa-edit"></i></button>
                                        </form>
                                    </td>
                                    <td>
                                        <form name="del-frm-<?php echo $i; ?>" method="post" action="delTec.php">
                                            <input type="hidden" name="tid" value="<?php echo $row['tid']; ?>">
                                            <button type="submit" class="btn text-danger"><i class="far fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                    <td>
                                        <form name="frm-hod-<?php echo $i; ?>">
                                            <input type="checkbox" name="hod" id="hod" value="<?php echo $row['hod']; ?>"
                                                <?php if ($row['hod'] == "Yes") echo "checked"; ?>
                                                onclick="myHOD('<?php echo $row['email_id']; ?>', '<?php echo $row['hod']; ?>', '<?php echo $row['tfname']; ?>')">
                                        </form>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                <?php
                } else {
                    echo "<p>No teacher details found.</p>";
                }
                ?>
        </div>
    </div>
</div>
<?php
            } catch (mysqli_sql_exception $e) {
                echo "Error: " . $e->getMessage();
            }
?>

<script>
function myHOD(email_id, hod, tfname) {
    $.ajax({
        method: "POST",
        url: "makehod.php",
        data: {
            email_id: email_id,
            hod: hod,
            tfname: tfname
        },
        success: function(result) {
            alert(result);
        }
    });
}

// Filter by name functionality
$(document).ready(function() {
    $('#nameFilter').on('keyup', function() {
        var filterText = $(this).val().toLowerCase();
        $('#teacherTable tbody tr').each(function() {
            // Combine first, middle, last name columns (indices 1,2,3)
            var firstName = $(this).find('td:eq(1)').text().toLowerCase();
            var middleName = $(this).find('td:eq(2)').text().toLowerCase();
            var lastName = $(this).find('td:eq(3)').text().toLowerCase();
            var fullName = firstName + ' ' + middleName + ' ' + lastName;

            if (fullName.indexOf(filterText) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
</script>

<?php require('include/footer.php'); ?>