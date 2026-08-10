<?php
require('include/header.php');
require_once('config.php');
if (empty($_POST['tid'])) {
    header('location:hod.php');
} else {
    try {
        $tid = $_POST['tid'];
        $del = "UPDATE teacher SET status='1' WHERE tid=$tid";
        $con->query($del);
        header('location:hod.php?msg=Teachers details recall succesfully');
    } catch (Exception $e) {
        header('location:hod.php?msg=' . $e->getMessage());
    }
}
require('include/footer.php');
?>