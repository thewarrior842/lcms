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
        <h5 class="mb-4">Previous Remove Departments</h5>
        <div class="table-responsive">
            <?php
            try {
                $src = "SELECT * from dept WHERE status='0' ORDER BY dname";
                $rs = $con->query($src);
                if (($rs->num_rows) > 0) {
            ?>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name of Department</th>
                                <th>Recall</th>
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
                                        <form name="del-frm-<?php echo $i; ?>" method="post" action="recallDept.php">
                                            <input type="hidden" name="did" value="<?php echo $row['did'] ?>">
                                            <button type="submit" class="btn text-success"><i class="fas fa-history"></i></button>
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
                    echo "Removed Department Details Successfully";
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