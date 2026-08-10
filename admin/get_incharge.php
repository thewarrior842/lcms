<?php
require_once('config.php');

$si_id = intval($_POST['si_id']);   // receive si_id from AJAX

$result = $con->query("SELECT si_id AS id, did, tid, year, academic_year 
                       FROM section_incharge 
                       WHERE si_id = $si_id");
echo json_encode($result->fetch_assoc());
?>