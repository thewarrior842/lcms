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
    <div class="recent-activity mt-4">
        <h5 class="mb-4">Gatekeepers</h5>
        <a href="newGat.php" class="btn btn-info">Add New</a>
        <a href="hisGat.php" class="btn btn-success">Delete History</a>
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
        <div class="table-responsive">
            <?php
            try {
                $src = "SELECT g.*, u.* FROM gatekeeper g INNER JOIN user u ON g.email_id = u.email_id WHERE g.status = '1'";
                $rs = $con->query($src);
                if ($rs->num_rows) {
            ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Hire Date</th>
                                <th>Last Login Date</th>
                                <th>Last Login Time</th>
                                <th>Update</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sn = 1;
                            while ($row = $rs->fetch_assoc()) {
                                // Handle Last Login Date
                                $lastLoginDate = (!empty($row['log_date']) && $row['log_date'] != '0000-00-00') 
                                                ? date('Y-m-d', strtotime($row['log_date'])) 
                                                : 'N/A';

                                // Handle Last Login Time
                                $lastLoginTime = 'N/A';
                                if (!empty($row['log_date']) && $row['log_date'] != '0000-00-00') {
                                    // Try to get time from log_time if exists, else extract from log_date
                                    if (!empty($row['log_time'])) {
                                        $lastLoginTime = date('H:i:s', strtotime($row['log_time']));
                                    } else {
                                        $lastLoginTime = date('H:i:s', strtotime($row['log_date']));
                                    }
                                }
                            ?>
                                <tr>
                                    <td><?php echo $sn++; ?></td>
                                    <td><?php echo htmlspecialchars($row['gfname']); ?></td>
                                    <td><?php echo htmlspecialchars($row['glname']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['gmobile']); ?></td>
                                    <td><?php echo htmlspecialchars($row['hire_date']); ?></td>
                                    <td><?php echo $lastLoginDate; ?></td>
                                    <td><?php echo $lastLoginTime; ?></td>
                                    <td>
                                        <form name="upd-frm-<?php echo $sn; ?>" method="post" action="updGat.php">
                                            <input type="hidden" name="gid" value="<?php echo $row['gid']; ?>">
                                            <button type="submit" class="btn text-primary"><i class="far fa-edit"></i></button>
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