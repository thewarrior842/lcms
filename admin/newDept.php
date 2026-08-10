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
        <h5 class="mb-4">Add New Department</h5>
        <div class="table-responsive">
            <div class="col-6">
                <form name="frm" method="post">
                    <div class="mb-3">
                        <label for="dname" class="form-label">Enter New Department Name</label>
                        <input type="text" class="form-control" id="dname" name="dname">
                    </div>
                    <input type="submit" name="ok" class="btn btn-primary" value="Add New Department">
                    <button><a href="dept.php">Back to view the department </a></button>
                 </form>
                <?php
                if(isset($_POST['ok'])){
                    $dname=$_POST['dname'];
                    try{
                        $src="SELECT dname from dept WHERE dname='$dname'";
                        $result=$con->query($src);
                        if($result->num_rows){
                            echo "This department is already exists";
                        }else{
                            $sql="INSERT INTO dept (dname) values ('$dname')";
                            $con->query($sql);
                            echo "New department add successfully";
                        }
                    }catch(Exception $e){
                        echo "Error: ".$e->getMessage();
                    }
                }
                ?>
            </div>
        </div>
    </div>

<?php require('include/footer.php') ?>