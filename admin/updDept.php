<?php
require('include/header.php');
require_once('config.php');
if (empty($_POST['did'])) {
    header('location:dept.php');
} else {
    try{
        $did = $_POST['did'];
        $src = "SELECT * FROM dept WHERE did=$did";
        $result = $con->query($src);
        $row = $result->fetch_assoc();
    }catch(Exception $e){
        echo "Error: ".$e->getMessage();    
    }
    
}
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
        <h5 class="mb-4">Update Departments</h5>
        <div class="table-responsive">
            <div class="col-6">
                <form name="frm" method="post">
                    <div class="mb-3">
                        <label for="dname" class="form-label">Enter New Department Name</label>
                        <input type="text" class="form-control" id="dname" name="dname" value="<?php echo $row['dname'] ?>">
                    </div>
                    <input type="hidden" name="did" value="<?php echo $row['did'] ?>">
                    <input type="submit" name="ok" class="btn btn-primary" value="Save Changes">
                    
                    <button><a href="dept.php">Back to view the department </a></button>
                </form>
                <?php
                if (isset($_POST['ok'])) {

                    try {
                        $dname = $_POST['dname'];
                        $upd="UPDATE dept SET dname='$dname' WHERE did=$did";
                        $con->query($upd);
                        header('location:dept.php?msg=Department details updated successfully');
                    } catch (Exception $e) {
                        echo "Error: " . $e->getMessage();
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <?php require('include/footer.php') ?>