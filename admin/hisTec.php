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
        <h5 class="mb-4">Previous Remove Teachers</h5>
        <div class="table-responsive">
            <?php
            try {
                $src = "SELECT u.log_time, u.log_date, t.*,d.dname FROM user u inner join teacher t on u.email_id=t.email_id inner join dept d on t.did=d.did WHERE t.status='0'";
                $rs = $con->query($src);
                if (($rs->num_rows) > 0) {
            ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Department</th>
                                <th>Hire Date</th>
                                <th>Address</th>
                                <th>HOD</th>
                                <th>Recall</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            while ($row = $rs->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td><?php echo $row['tfname'] ?></td>
                                    <td><?php echo $row['tmname'] ?></td>
                                    <td><?php echo $row['tlname'] ?></td>
                                    <td><?php echo $row['email_id'] ?></td>
                                    <td><?php echo $row['tmobile'] ?></td>
                                    <td><?php echo $row['dname'] ?></td>
                                    <td><?php echo $row['hire_date'] ?></td>
                                    <td><?php echo $row['taddress'] ?></td>
                                    <td><?php echo $row['hod'] ?></td>
                                    <td>
                                        <form name="del-frm-<?php echo $i; ?>" method="post" action="recallTec.php">
                                            <input type="hidden" name="tid" value="<?php echo $row['tid'] ?>">
                                            <button type="submit" class="btn text-success"><i class="fas fa-history"></i></button>
                                        </form>
                                    </td>
                                    <td>
                                        <form name="del-frm-<?php echo $i; ?>" method="post" action="delTec.php">
                                            <input type="hidden" name="did" value="<?php echo $row['did'] ?>">
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
                    $i++;
                }else{
                    echo "Removed Teachers Details Successfully";
                }
                ?>
        </div>

    </div>
</div>
<?php
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
?>

<?php require('include/footer.php') ?>