<?php
require('include/header.php');
require_once('config.php');
try{
    $dsrc="SELECT * from dept WHERE status='1' ORDER BY dname";
    $drs=$con->query($dsrc);
}catch(Exception $e){
    echo "Error : ".$e->getMessage();
}
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
        <h5 class="mb-4">Add New Teachers</h5>
        <div class="table-responsive">
            <form class="row g-3" name="frm" method="post">
                <div class="col-md-6">
                    <label for="tfname" class="form-label">Enter New Teachers First Name</label>
                    <input type="text" class="form-control" id="tfname" name="tfname">
                </div>
                <div class="col-md-6">
                    <label for="tmname" class="form-label">Enter New Teachers Middle Name</label>
                    <input type="text" class="form-control" id="tmname" name="tmname">
                </div>
                <div class="col-md-6">
                    <label for="tlname" class="form-label">Enter New Teachers Last Name</label>
                    <input type="text" class="form-control" id="tlname" name="tlname">
                </div>
                <div class="col-md-6">
                    <label for="email_id" class="form-label">Enter New Teachers Email</label>
                    <input type="email" class="form-control" id="email_id" name="email_id">
                </div>
                <div class="col-md-6">
                    <label for="tmobile" class="form-label">Enter New Teachers Mobile</label>
                    <input type="number" class="form-control" id="tmobile" name="tmobile">
                </div>
                <div class="col-md-6">
                    <label for="did" class="form-label">Select Teachers Department</label>
                    <select name="did" id="did" class="form-control">
                        <option value="">-Select Department-</option>
                        <?php
                        while($drow=$drs->fetch_assoc()){
                            ?>
                            <option value="<?php echo $drow['did'] ?>"><?php echo $drow['dname'] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="hire_date" class="form-label">Enter New Teachers Hire Date</label>
                    <input type="date" class="form-control" id="hire_date" name="hire_date">
                </div>
                <div class="col-12">
                    <label for="taddress" class="form-label">Enter New Teachers Address</label>
                    <input type="text" class="form-control" id="taddress" name="taddress">
                </div>
                <input type="submit" name="ok" class="btn btn-primary" value="Add New Teacher">
            </form>
            <?php
            if(isset($_POST['ok'])){
                $tfname=$_POST['tfname'];
                $tfname=$_POST['tmname'];
                $tlname=$_POST['tlname'];
                $email_id=$_POST['email_id'];
                $tmobile=$_POST['tmobile'];
                $did=$_POST['did'];
                $hire_date=$_POST['hire_date'];
                $taddress=$_POST['taddress'];
                
                try{
                    $src="SELECT email_id from teacher WHERE email_id='$email_id' ";
                    $result=$con->query($src);
                    if($result->num_rows){
                        echo "This Teacher is already exists";
                    }else{
                        $sql="INSERT INTO teacher (tfname,tlname,email_id,tmobile,did,hire_date,taddress) values ('$tfname','$tlname','$email_id',$tmobile,$did,'$hire_date','$taddress')";
                        $con->query($sql);

                        $sql1="INSERT INTO user(email_id)VALUES('$email_id')";
                        $con->query($sql1);
                        echo "New Teacher add successfully";

                    }

                }catch(mysqli_sql_exception $e){
                    echo "Error: ".$e->getMessage();
                }
            }
            ?>
        </div>
    </div>

<?php require('include/footer.php') ?>