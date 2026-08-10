<?php
require_once('config.php');
if (empty($_POST['gid'])) {
    echo "No Gatekeepers found";
    header('location:gatekeeper.php');
} else {
    try {
        $gid = $_POST['gid'];
        $del = "UPDATE gatekeeper SET status='0' WHERE gid=$gid";
        $con->query($del);
        header('location:gatekeeper.php?msg=Gatekeeper details remove succesfully');
    } catch (mysqli_sql_exception $e) {
        header('location:gatekeeper.php?msg=' . $e->getMessage());
    }
}

?>