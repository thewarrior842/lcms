<?php
require('include/header.php');
require_once('config.php');
?>

<div class="main-content">
    <h4>Section Incharge Wise Student Details</h4>

    <form method="post" class="col-6">

        <!-- Department -->
        <div class="mb-3">
            <label>Select Department</label>
            <select name="did" id="department" class="form-control" required>
                <option value="">-- Select Department --</option>
                <?php
                $dept = $con->query("SELECT * FROM dept");
                while($d = $dept->fetch_assoc()){
                    echo "<option value='".$d['did']."'>".$d['dname']."</option>";
                }
                ?>
            </select>
        </div>

        <!-- Section Incharge -->
        <div class="mb-3">
            <label>Select Section Incharge</label>
            <select name="tid" id="teacher" class="form-control" required>
                <option value="">-- Select Department First --</option>
            </select>
        </div>

        <input type="submit" name="search" class="btn btn-primary" value="View Students">
    </form>
    <?php
if(isset($_POST['search'])){

    $did = $_POST['did'];
    $tid = $_POST['tid'];
    $current_year = date("Y");

    $sql = "SELECT 
                s.sid,
                s.rollno,
                s.fname,
                S.mname,
                s.lname,
                s.semester,
                CASE 
                    WHEN s.semester IN (1,2) THEN '1st'
                    WHEN s.semester IN (3,4) THEN '2nd'
                    WHEN s.semester IN (5,6) THEN '3rd'
                    
                END AS academic_year
            FROM student s
            JOIN section_incharge si 
                ON s.did = si.did
                AND si.tid = '$tid'
                AND si.did = '$did'
                AND si.year = '$current_year'
                AND si.academic_year = 
                    CASE 
                        WHEN s.semester IN (1,2) THEN '1st'
                        WHEN s.semester IN (3,4) THEN '2nd'
                        WHEN s.semester IN (5,6) THEN '3rd'
                    END
            WHERE s.did = '$did'";

    $result = $con->query($sql);

    if($result->num_rows > 0){
        echo "<h5 class='mt-4'>Student List</h5>";
        echo "<table class='table table-bordered mt-2'>
                <tr>
                    <th>Roll No</th>
                    <th>Name</th>
                    <th>Semester</th>
                    <th>Academic Year</th>
                </tr>";

        while($row = $result->fetch_assoc()){
            echo "<tr>
                    <td>".$row['rollno']."</td>
                    <td>".$row['fname']." ".$row['mname']." ".$row['lname']."</td>
                    <td>".$row['semester']."</td>
                    <td>".$row['academic_year']."</td>
                  </tr>";
        }

        echo "</table>";
    } else {
        echo "<div class='alert alert-warning mt-3'>
                No students found under this incharge.
              </div>";
    }
}
?>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$('#department').on('change', function(){
    var did = $(this).val();

    if(did != ''){
        $.ajax({
            url: "fetch_incharge.php",
            type: "POST",
            data: {did: did},
            success: function(data){
                $('#teacher').html(data);
            }
        });
    } else {
        $('#teacher').html('<option value="">-- Select Department First --</option>');
    }
});
</script>
<?php require('include/footer.php'); ?>