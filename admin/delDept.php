<?php
require('include/header.php');
require_once('config.php');
if (empty($_POST['did'])) {
    header('location:dept.php');
} else {
    try {
        $did = $_POST['did'];
        $del = "UPDATE dept SET status='0' WHERE did=$did";
        $con->query($del);
        header('location:dept.php?msg=Department details remove succesfully');
    } catch (Exception $e) {
        header('location:dept.php?msg=' . $e->getMessage());
    }
}
require('include/footer.php');
?>