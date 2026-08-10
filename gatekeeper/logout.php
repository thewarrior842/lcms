<?php
session_start();          // VERY IMPORTANT
session_unset();          // remove all session variables
session_destroy();        // destroy session

header('Location: ../index.php');
exit();
?>