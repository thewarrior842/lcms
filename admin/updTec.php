<?php
require('include/header.php');
require_once('config.php');
if (empty($_POST['tid'])) {
    header('location:hod.php');
} else {
    try {
        $tid = $_POST['tid'];
        $src = "SELECT t.*, d.dname FROM teacher t INNER JOIN dept d ON t.did=d.did WHERE tid=$tid";
        $result = $con->query($src);
        $row = $result->fetch_assoc();
        $dsrc="SELECT * from dept WHERE status='1' ORDER BY dname";
        $drs=$con->query($dsrc);
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    }
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
        <h5 class="mb-4">Update Teachers</h5>
        <div class="table-responsive">
            <form class="row g-3" name="frm" method="post">
                <div class="col-md-6">
                    <label for="tfname" class="form-label">Enter New Teachers First Name</label>
                    <input type="text" class="form-control" id="tfname" name="tfname" value="<?php echo $row['tfname'] ?>">
                </div>
                <div class="col-md-6">
                    <label for="tmname" class="form-label">Enter New Teachers Middle Name</label>
                    <input type="text" class="form-control" id="tmname" name="tmname" value="<?php echo $row['tmname'] ?>">
                </div>
                <div class="col-md-6">
                    <label for="tlname" class="form-label">Enter New Teachers Last Name</label>
                    <input type="text" class="form-control" id="tlname" name="tlname" value="<?php echo $row['tlname'] ?>">
                </div>
                <div class="col-md-6">
                    <label for="email_id" class="form-label">Enter New Teachers Email</label>
                    <input type="email"  class="form-control" id="email_id" name="email_id" value="<?php echo $row['email_id'] ?>">
                </div>
                <div class="col-md-6">
                    <label for="tmobile" class="form-label">Enter New Teachers Mobile</label>
                    <input type="number" class="form-control" id="tmobile" name="tmobile" value="<?php echo $row['tmobile'] ?>">
                </div>
                <div class="col-md-6">
                    <label for="did" class="form-label">Select Teachers Department</label>
                    <select name="did" id="did" class="form-control">
                        <?php
                        while ($drow = $drs->fetch_assoc()) {
                            $selcted=($drow['did']==$row['did'])? 'selected' : '';
                        ?>
                            <option value="<?php echo $drow['did'] ?>" <?php echo $selcted; ?>><?php echo $drow['dname'] ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="hire_date" class="form-label">Enter New Teachers Hire Date</label>
                    <input type="date" class="form-control" id="hire_date" name="hire_date" value="<?php echo $row['hire_date'] ?>">
                </div>
                <div class="col-12">
                    <label for="taddress" class="form-label">Enter New Teachers Address</label>
                    <input type="text" class="form-control" id="taddress" name="taddress" value="<?php echo $row['taddress'] ?>" >
                </div>
                <input type="hidden" name="tid" value="<?php echo $row['tid'] ?>">
                <input type="submit" name="ok" class="btn btn-primary" value="Save Changes">
            </form>
            <?php
            if (isset($_POST['ok'])) {
                try {
                    $tfname = $_POST['tfname'];
                    $tmname = $_POST['tmname'];
                    $tlname = $_POST['tlname'];
                    $email_id = $_POST['email_id'];
                    $tmobile = $_POST['tmobile'];
                    $did = $_POST['did'];
                    $hire_date = $_POST['hire_date'];
                    $taddress = $_POST['taddress'];
                    $upd = "UPDATE teacher SET tfname='$tfname' , tlname='$tlname',email_id='$email_id', tmobile='$tmobile', did='$did', hire_date='$hire_date', taddress='$taddress' ,tmname='$tmname' WHERE tid=$tid";
                    $con->query($upd);
                    // $uupd="UPDATE user SET email_id='$email_id' WHERE uid=$tid";
                    // $con->query($uupd);
                    ?>
                    <script>
                        window.location='hod.php?msg=Teachers details updated successfully';
                    </script>
                    <?php
                } catch (mysqli_sql_exception $e) {
                    echo "Error: " . $e->getMessage();
                }
            }
            ?>
        </div>
    </div>

    <?php require('include/footer.php') ?>