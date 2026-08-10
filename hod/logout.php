<?php
require('config.php');
session_destroy();
header('Location: ../index.php');
// header('location: login.php');
?>