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
        <h5 class="mb-4">Add New Gatekeeper</h5>
        <div class="table-responsive">
            <form class="row g-3" name="frm" method="post">
                <div class="col-md-6">
                    <label for="gfname" class="form-label">Enter New Gatekeeper First Name</label>
                    <input type="text" class="form-control" id="gfname" name="gfname">
                </div>
                <div class="col-md-6">
                    <label for="glname" class="form-label">Enter New Gatekeeper Last Name</label>
                    <input type="text" class="form-control" id="glname" name="glname">
                </div>
                <div class="col-md-6">
                    <label for="email_id" class="form-label">Enter New Gatekeeper Email</label>
                    <input type="email" class="form-control" id="email_id" name="email_id">
                </div>
                <div class="col-md-6">
                    <label for="gmobile" class="form-label">Enter New Gatekeeper Mobile</label>
                    <input type="number" class="form-control" id="gmobile" name="gmobile">
                </div>
                <div class="col-md-6">
                    <label for="hire_date" class="form-label">Enter New Gatekeeper Hire Date</label>
                    <input type="date" class="form-control" id="hire_date" name="hire_date">
                </div>
                <div class="col-12">
                    <label for="gaddress" class="form-label">Enter New Gatekeeper Address</label>
                    <input type="text" class="form-control" id="gaddress" name="gaddress">
                </div>
                <input type="submit" name="ok" class="btn btn-primary" value="Add New Gatekeeper">
            </form>
            <?php
            if(isset($_POST['ok'])){
                $gfname=$_POST['gfname'];
                $glname=$_POST['glname'];
                $email_id=$_POST['email_id'];
                $gmobile=$_POST['gmobile'];
                $hire_date=$_POST['hire_date'];
                $gaddress=$_POST['gaddress'];
                
                try{
                    $src="SELECT email_id FROM gatekeeper WHERE email_id='$email_id' ";
                    $src1="SELECT email_id FROM user WHERE email_id='$email_id'";
                    $urs=$con->query($src1);
                    $result=$con->query($src);
                    if($result->num_rows || $urs->num_rows){
                        echo "This Gatekeeper is already exists";
                    }else{
                        $sql="INSERT INTO gatekeeper (gfname,glname,email_id,gmobile,hire_date,gaddress) values ('$gfname','$glname','$email_id',$gmobile,'$hire_date','$gaddress')";
                        $con->query($sql);

                        $sql1="INSERT INTO user(email_id, role) VALUES('$email_id', 'gatekeeper')";
                        $con->query($sql1);
                        echo "New Gatekeeper add successfully";

                    }

                }catch(mysqli_sql_exception $e){
                    echo "Error: ".$e->getMessage();
                }
            }
            ?>
        </div>
    </div>

<?php require('include/footer.php') ?>