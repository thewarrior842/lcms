<?php
session_start();
$host = "sql302.infinityfree.com"; // 127.0.0.1
$dbuser = "if0_41326526";
$dbpass = "Deeplcms";
$dbname = "if0_41326526_lcms";
$port = "3306";
try {
    // $con=mysqli_connect($host, $dbuser, $dbpass, $dbname, $port);
    $con = new mysqli($host, $dbuser, $dbpass, $dbname, $port);
} catch (Exception $e) {
    echo $e->getMessage();
}
