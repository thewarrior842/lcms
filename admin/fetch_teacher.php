<?php
require_once('config.php');

if(isset($_POST['did'])){
    
    $did = $_POST['did'];

    $query = "SELECT * FROM teacher 
              WHERE did='$did' AND status='1'";

    $result = $con->query($query);

    if($result->num_rows > 0){
        echo '<option value="">-- Select Teacher --</option>';
        while($row = $result->fetch_assoc()){
            echo '<option value="'.$row['tid'].'">'
                .$row['tfname'].' '.$row['tmname'].' '.$row['tlname'].
                '</option>';
        }
    } else {
        echo '<option value="">No Teacher Found</option>';
    }
}
?>