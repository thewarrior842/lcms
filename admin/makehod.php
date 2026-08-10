<?php
require_once('config.php');
require_once('auth.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);



$email_id = $_POST['email_id'];
$hod = $_POST['hod'];
$p = '123456';
$role = 'hod';
if ($hod == "No") {
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Gmail SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'attcprincipal22@gmail.com';  // YOUR Gmail address
        $mail->Password   = 'pprp hldj vwkn madb';     // YOUR Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        
        $pwd = password_hash($p, PASSWORD_DEFAULT);
        $tupd = "UPDATE teacher SET hod='Yes' WHERE email_id='$email_id'";
        $con->query($tupd);
        $uupd = "UPDATE user SET role='hod', pwd='$pwd' WHERE email_id='$email_id'";
        $con->query($uupd);
        
        // Recipients
        $mail->setFrom('attcprincipal22@gmail.com', 'ATTC Admin');
        $semail=$email_id;
        $sname=$_POST['tfname'];
        $spassword=$p;

        $mail->addAddress($semail, $sname);

        // Content
        $mail->isHTML(false);
        $mail->Subject = 'Your Login Credencial';
        $mail->Body    = "Hello $sname sir/ma'am,\nYour Login Credencial.\n\nYour account has been created.\n\nEmail: $semail\nPassword: $spassword\n\nPlease log in and change your password according to your memory because of security and privacy reason.\n\n Open in browser https://lcms.infinityfree.me/ .\n \n\nRegards,\nDeep Karmakar\nDCE 6th Semester\nPhone no: 7797682612";

        $mail->send();
        
        echo "Teacher access to login";
    } catch (mysqli_sql_exception $e) {
        echo $e->getMessage();
    }
} elseif ($hod == "Yes") {
    try {
        $tupd = "UPDATE teacher SET hod='No' WHERE email_id='$email_id'";
        $con->query($tupd);
        echo "Teacher denied to login";
    } catch (mysqli_sql_exception $e) {
        echo $e->getMessage();
    }
}
