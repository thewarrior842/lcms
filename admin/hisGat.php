<?php
require('include/header.php');
require_once('config.php');
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

    <!-- Back Menu Link to Gatekeeper.php -->
    <div class="mb-3">
        <a href="gatekeeper.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Gatekeepers
        </a>
    </div>

    <div class="recent-activity mt-4">
        <h5 class="mb-4">Previous Removed Gatekeepers</h5>
        <div class="table-responsive">
            <?php
            try {
                $src = "SELECT u.log_time, u.log_date, g.* FROM user u INNER JOIN gatekeeper g ON u.email_id=g.email_id WHERE g.status='0'";
                $rs = $con->query($src);
                if (($rs->num_rows) > 0) {
            ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Sl No.</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Hire Date</th>
                                <th>Last Login Date</th>
                                <th>Last Login Time</th>
                                <th>Recall</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sn = 1;
                            while ($row = $rs->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td><?php echo $sn++; ?></td>
                                    <td><?php echo htmlspecialchars($row['gfname']); ?></td>
                                    <td><?php echo htmlspecialchars($row['glname']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['gmobile']); ?></td>
                                    <td><?php echo htmlspecialchars($row['hire_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['log_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['log_time']); ?></td>
                                    <td>
                                        <form name="del-frm-<?php echo $sn; ?>" method="post" action="recallGat.php">
                                            <input type="hidden" name="gid" value="<?php echo $row['gid']; ?>">
                                            <button type="submit" class="btn text-success"><i class="fas fa-history"></i></button>
                                        </form>
                                    </td>
                                    <td>
                                        <form name="del-frm-<?php echo $sn; ?>" method="post" action="delGat.php">
                                            <input type="hidden" name="gid" value="<?php echo $row['gid']; ?>">
                                            <button type="submit" class="btn text-danger"><i class="far fa-trash-alt"></i></button>
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
                    echo "No Gatekeeper details found";
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

<?php require('include/footer.php'); ?>