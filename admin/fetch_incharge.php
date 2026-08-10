<?php
require_once('config.php');

if(isset($_POST['did'])){

    $did = $_POST['did'];
    $current_year = date("Y");

    $query = "SELECT DISTINCT t.tid, t.tfname, t.tlname
              FROM teacher t
              JOIN section_incharge si 
                    ON t.tid = si.tid
              WHERE si.did = '$did'
              AND si.year = '$current_year'";

    $result = $con->query($query);

    if($result->num_rows > 0){
        echo '<option value="">-- Select Incharge --</option>';
        while($row = $result->fetch_assoc()){
            echo '<option value="'.$row['tid'].'">'
                .$row['tfname'].' '.$row['tlname'].
                '</option>';
        }
    } else {
        echo '<option value="">No Incharge Assigned</option>';
    }
}
?>