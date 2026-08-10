<?php
require('include/header.php');
require_once('config.php');

if (empty($_POST['gid'])) {
    // Use JavaScript redirect if header might fail
    echo '<script>window.location.href = "gatekeeper.php";</script>';
    exit;
} else {
    try {
        $gid = (int)$_POST['gid'];
        $del = "UPDATE gatekeeper SET status='1' WHERE gid=$gid";
        $con->query($del);
        
        // JavaScript redirect to hisGat.php with success message
        echo '<script>window.location.href = "hisGat.php?msg=Gatekeeper details recalled successfully";</script>';
        exit;
    } catch (Exception $e) {
        // JavaScript redirect with error message
        $errorMsg = urlencode($e->getMessage());
        echo '<script>window.location.href = "hisGat.php?msg=' . $errorMsg . '";</script>';
        exit;
    }
}

require('include/footer.php');
?>