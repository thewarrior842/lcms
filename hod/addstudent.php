<?php
require('include/header.php');
require_once('config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $dsrc = "SELECT * from dept WHERE status='1' ORDER BY dname";
    $drs = $con->query($dsrc);
} catch (Exception $e) {
    echo "Error : " . $e->getMessage();
}
?>
<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="header">
        <h2>Students</h2>
        <div class="user-info">
            <div class="user-avatar">
                <p><?php echo substr($_SESSION['h_info']['tfname'], 0, 1) . substr($_SESSION['h_info']['tmname'], 0, 1). substr($_SESSION['h_info']['tlname'], 0, 1) ?></p>
            </div>
            <div>
                <p>Welcome back, <?php echo $_SESSION['h_info']['tfname'] . " " . $_SESSION['h_info']['tmname']. " " . $_SESSION['h_info']['tlname']; ?>!</p>
            </div>
        </div>
    </div>


    <!-- Settings Form -->
    <div class="recent-activity">
        <!-- Main Form -->
        <form name="add-std-frm" id="settingsForm" method="post" enctype="multipart/form-data">
            <div class="form-header">
                <h5>Add Current Semester Students</h5>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="did" class="form-label">Select Student Department</label>
                        <select name="did" id="did" class="form-control">
                            <option value="">-Select Department-</option>
                            <?php
                            while ($drow = $drs->fetch_assoc()) {
                                ?>
                                <option value="<?php echo $drow['did'] ?>"><?php echo $drow['dname'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="csv_file" class="form-label">Select excel sheet of Students</label>
                        <input type="file" class="form-control" name="csv_file" id="csv_file">
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <!-- <button type="button" class="btn" id="cancelBtn">
                    <i class="fas fa-times me-2"></i> Cancel
                </button> -->
                <input type="submit" id="ok" name="ok" value="Add" class="btn btn-primary">
            </div>
        </form>
        <?php
        if (isset($_POST['ok'])) {
            // echo "Hello";
            $did=$_POST['did'];
            if ($_FILES['csv_file']['name']) {
                $filename = $_FILES['csv_file']['tmp_name'];
                $file = fopen($filename, "r");
                fgetcsv($file); // skip header
        
                while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
                    list($fname, $mname, $lname, $rollno, $fathername, $mothername, $mobile, $email_id, $gender, $semester, $sem_year) = $data;

                    // Generate a unique password for each student
                    $spassword = rand(000000,999999);
                    $hashed = password_hash($spassword, PASSWORD_DEFAULT);
                    $query = "INSERT INTO student 
                (fname, mname, lname, rollno, did, fathername, mothername, mobile, email_id, pwd, gender, semester, sem_year)
                VALUES 
                ('$fname', '$mname', '$lname', '$rollno', '$did', '$fathername', '$mothername', '$mobile', '$email_id', '$hashed', '$gender', '$semester', '$sem_year')";


                    if (mysqli_query($con, $query)) {
                        // Send email
                        try {
                            $mail = new PHPMailer(true);
                            $mail->isSMTP();
                            $mail->Host = 'smtp.gmail.com';
                            $mail->SMTPAuth = true;
                            $mail->Username = 'attcprincipal22@gmail.com';  // Your Gmail
                            $mail->Password = 'pprp hldj vwkn madb';     // Gmail app password
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Port = 587;

                            $mail->setFrom('attcprincipal22@gmail.com', 'Student Management');
                            $mail->addAddress($email_id, $fname);

                            $mail->isHTML(false);
                            $mail->Subject = 'Your Student Portal Login Credentials';
                            $mail->Body = "Hello $fname $mname $lname,\n\nYour student account has been created on https://lcms.infinityfree.me/.\n\nEmail: $email_id\nPassword: $spassword\n\nPlease log in and change your password.\n\nRegards,\nStudent Management Team";

                            $mail->send();
                        } catch (Exception $e) {
                            echo "Mailer Error for $email_id: {$mail->ErrorInfo}<br>";
                        }
                    } else {
                        echo "Database insert error for $email_id: " . mysqli_error($connection) . "<br>";
                    }
                }
                fclose($file);
                echo "Upload complete";
            }
        }
        ?>
    </div>

    <?php
    require('include/footer.php');

    ?>

    <!-- <script>
        // Form submission handler
        document.getElementById('settingsForm').addEventListener('submit', function (e) {
            e.preventDefault();

            // Show success message
            const successMessage = document.getElementById('successMessage');
            successMessage.style.display = 'block';

            // Hide message after 5 seconds
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 5000);

            // In a real application, you would submit the form data to a server here
            console.log('Form submitted with data:', {
                firstName: document.getElementById('firstName').value,
                lastName: document.getElementById('lastName').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                department: document.getElementById('department').value,
                username: document.getElementById('username').value,
                timezone: document.getElementById('timezone').value,
                emailNotifications: document.getElementById('emailNotifications').checked,
                smsNotifications: document.getElementById('smsNotifications').checked,
                pushNotifications: document.getElementById('pushNotifications').checked,
                bio: document.getElementById('bio').value
            });
        });

        // Cancel button handler
        document.getElementById('cancelBtn').addEventListener('click', function () {
            if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
                // Reset form to original values
                document.getElementById('settingsForm').reset();
                document.getElementById('successMessage').style.display = 'none';
            }
        });

        // Form validation
        const form = document.getElementById('settingsForm');
        const requiredFields = form.querySelectorAll('[required]');

        form.addEventListener('submit', function (e) {
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'var(--danger)';
                } else {
                    field.style.borderColor = '#ced4da';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    </script> -->
    </body>

    </html>