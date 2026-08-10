<?php
require('include/header.php');
require_once('config.php');
?>

<div class="main-content">
    <h4>Assign Section Incharge</h4>

    <div class="col-6">
        <form method="post">

            <!-- Department Dropdown -->
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

            <!-- Academic Year Dropdown -->
            <div class="mb-3">
                <label>Select Academic Year</label>
                <select name="academic_year" class="form-control" required>
                    <option value="">-- Select Year --</option>
                    <option value="1st">1st Year</option>
                    <option value="2nd">2nd Year</option>
                    <option value="3rd">3rd Year</option>
                </select>
            </div>

            <!-- Teacher Dropdown (AJAX Loaded) -->
            <div class="mb-3">
                <label>Select Teacher</label>
                <select name="tid" id="teacher" class="form-control" required>
                    <option value="">-- Select Department First --</option>
                </select>
            </div>

            <input type="submit" name="assign" class="btn btn-primary" value="Assign Incharge">
        </form>

<?php
if(isset($_POST['assign'])){

    $did = $_POST['did'];
    $tid = $_POST['tid'];
    $academic_year = $_POST['academic_year'];
    $current_year = date("Y");

    // Check if already assigned for same department + academic year + calendar year
    $check = $con->query("SELECT * FROM section_incharge 
                          WHERE did='$did' 
                          AND academic_year='$academic_year'
                          AND year='$current_year'");

    if($check->num_rows > 0){
        echo "<div class='alert alert-danger mt-3'>
                Incharge already assigned for this department & year!
              </div>";
    } else {

        $sql = "INSERT INTO section_incharge (did, year, tid, academic_year)
                VALUES ('$did', '$current_year', '$tid', '$academic_year')";

        if($con->query($sql)){
            echo "<div class='alert alert-success mt-3'>
                    Section Incharge Assigned Successfully!
                  </div>";
        } else {
            echo "Error: " . $con->error;
        }
    }
}
?>

    </div>
</div>

<!-- AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$('#department').on('change', function(){
    var did = $(this).val();

    if(did != ''){
        $.ajax({
            url: "fetch_teacher.php",
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