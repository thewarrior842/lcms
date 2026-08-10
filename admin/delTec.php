<?php
require_once('config.php');
if (empty($_POST['tid'])) {
    echo "No teachers found";
    header('location:hod.php');
} else {
    try {
        $tid = $_POST['tid'];
        $del = "UPDATE teacher SET status='0' WHERE tid=$tid";
        $con->query($del);
        header('location:hod.php?msg=Teacher details remove succesfully');
    } catch (mysqli_sql_exception $e) {
        header('location:hod.php?msg=' . $e->getMessage());
    }
}

?>