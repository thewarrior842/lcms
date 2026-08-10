<?php
require('include/header.php');
require_once('config.php');
?>
<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Department</h2>

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
        <h5 class="mb-4">Departments</h5>
        <a href="newDept.php" class="btn btn-info">Add New</a>
        <a href="hisDept.php" class="btn btn-success">Delete History</a>
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
                $src = "SELECT * from dept WHERE status='1' ORDER BY dname";
                $rs = $con->query($src);
                if (($rs->num_rows) > 0) {
            ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name of Department</th>
                                <th>Update</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            while ($row = $rs->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td><?php echo $row['dname'] ?></td>
                                    <td>
                                        <form name="upd-frm-<?php echo $i; ?>" method="post" action="updDept.php">
                                            <input type="hidden" name="did" value="<?php echo $row['did'] ?>">
                                            <button type="submit" class="btn text-primary"><i class="far fa-edit"></i></button>
                                        </form>
                                    </td>
                                    <td>
                                        <form name="del-frm-<?php echo $i; ?>" method="post" action="delDept.php">
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
                    echo "No department details found";
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