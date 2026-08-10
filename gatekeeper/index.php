<?php
require_once('config.php');
require('include/header.php');
date_default_timezone_set('Asia/Kolkata');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/**
 * Process a student entry
 * @param string $rollno
 * @param bool $is_manual - if true, also insert into no_id table
 * @return string HTML formatted messages
 */
function processEntry($rollno, $is_manual = false) {
    global $con;
    $output = '';

    try {
        $rollno = trim($rollno);
        if (empty($rollno)) {
            throw new Exception("Roll Number Missing");
        }

        $log_date = date("Y-m-d");
        $log_time = date("H:i:s");
        $sem_year = date("Y");

        // Duplicate check for today
        $check = $con->prepare(
            "SELECT entry_log_id FROM entry_log 
             WHERE rollno=? AND log_date=?"
        );
        $check->bind_param("ss", $rollno, $log_date);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            return "⚠️ Already Logged Today";
        }

        // Get student details
        $student = $con->prepare(
            "SELECT fname, mname, lname, email_id, did, semester 
             FROM student 
             WHERE rollno=? AND sem_year=?"
        );
        $student->bind_param("ss", $rollno, $sem_year);
        $student->execute();
        $res = $student->get_result();

        if ($res->num_rows == 0) {
            throw new Exception("Student Not Found");
        }

        $stu = $res->fetch_assoc();
        $student_name = $stu['fname'] . " " . $stu['mname'] . " " . $stu['lname'];
        $email_id     = $stu['email_id'];
        $did          = $stu['did'];
        $semester     = $stu['semester'];

        // Map semester to academic year (1st,2nd,3rd)
        if (in_array($semester, [1, 2]))      $academic_year = "1st";
        elseif (in_array($semester, [3, 4]))  $academic_year = "2nd";
        elseif (in_array($semester, [5, 6]))  $academic_year = "3rd";

        // Late check (after 08:45)
        $is_late = ($log_time > "08:45:00") ? 1 : 0;

        // Insert into entry_log
        $insert = $con->prepare(
            "INSERT INTO entry_log (rollno, log_date, log_time, is_late)
             VALUES (?, ?, ?, ?)"
        );
        $insert->bind_param("sssi", $rollno, $log_date, $log_time, $is_late);
        $insert->execute();
        $output .= "✅ Entry Recorded";

        // If manual, also insert into no_id table
        if ($is_manual) {
            $no_id_insert = $con->prepare(
                "INSERT INTO no_id (rollno, log_date, log_time) VALUES (?, ?, ?)"
            );
            $no_id_insert->bind_param("sss", $rollno, $log_date, $log_time);
            $no_id_insert->execute();
            $output .= "<br>📋 Manual entry logged ";
        }

        // Late handling
        if ($is_late == 1) {
            $output .= "<br>⏰ Late Entry";

            // Monthly late count
            $late = $con->prepare(
                "SELECT COUNT(*) FROM entry_log
                 WHERE rollno=?
                 AND is_late=1
                 AND MONTH(log_date)=MONTH(CURDATE())
                 AND YEAR(log_date)=YEAR(CURDATE())"
            );
            $late->bind_param("s", $rollno);
            $late->execute();
            $late->bind_result($late_count);
            $late->fetch();
            $late->close();
            $output .= "<br>Late Count: " . $late_count;

            // Send email to student
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'attcprincipal22@gmail.com';
            $mail->Password   = 'pprp hldj vwkn madb';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('attcprincipal22@gmail.com', 'ATTC Gatekeeper');
            $mail->addAddress($email_id, $student_name);
            $mail->isHTML(true);
            $mail->Subject = "Late Entry Alert";
            $mail->Body = "
                Hello $student_name,<br><br>
                You entered late today.<br>
                Date: $log_date <br>
                Time: $log_time <br><br>
                Total Late This Month: <b>$late_count</b><br><br>
                Please be punctual.<br><br>
                ATTC Gatekeeper
            ";
            $mail->send();
            $output .= "<br>📧 Student Email Sent";

            // 3 late = 1 absent
            if ($late_count % 3 == 0) {
                $absent = $con->prepare(
                    "INSERT INTO attendance (rollno, status, date)
                     VALUES (?, 'ABSENT', CURDATE())"
                );
                $absent->bind_param("s", $rollno);
                $absent->execute();
                $output .= "<br>🚫 Absent Marked";

                // Notify section incharge
                $incharge = $con->prepare(
                    "SELECT t.email_id
                     FROM section_incharge si
                     JOIN teacher t ON si.tid = t.tid
                     WHERE si.did=? 
                     AND si.academic_year=? 
                     AND si.year=?"
                );
                $incharge->bind_param("sss", $did, $academic_year, $sem_year);
                $incharge->execute();
                $inc_res = $incharge->get_result();

                if ($inc_res->num_rows > 0) {
                    $row = $inc_res->fetch_assoc();
                    $incharge_email = $row['email_id'];

                    $mail2 = new PHPMailer(true);
                    $mail2->isSMTP();
                    $mail2->Host       = 'smtp.gmail.com';
                    $mail2->SMTPAuth   = true;
                    $mail2->Username   = 'attcprincipal22@gmail.com';
                    $mail2->Password   = 'pprp hldj vwkn madb';
                    $mail2->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail2->Port       = 587;

                    $mail2->setFrom('attcprincipal22@gmail.com', 'ATTC Gatekeeper');
                    $mail2->addAddress($incharge_email, "Section Incharge");
                    $mail2->isHTML(true);
                    $mail2->Subject = "Student Marked Absent (3 Lates Completed)";
                    $mail2->Body = "
                        Student: $student_name <br>
                        Roll No: $rollno <br>
                        Academic Year: $academic_year <br>
                        Total Late This Month: $late_count <br>
                        Date: $log_date <br><br>
                        Student has completed 3 late entries and is marked ABSENT.
                        <br><br>
                        ATTC Gatekeeper System
                    ";
                    $mail2->send();
                    $output .= "<br>📧 Section Incharge Notified";
                } else {
                    $output .= "<br>⚠️ No section incharge found for this department/year.";
                }
            }
        }
    } catch (Exception $e) {
        $output .= "Error: " . $e->getMessage();
    }

    return $output;
}
?>



<div class="dashboard-section">
    <div class="row">
        <div class="col-12">
            <!-- Single form with radio and input -->
            <form method="post" id="entryForm">
                <div class="mb-3">
                    <label class="form-label">Select Mode:</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="entry_mode" id="modeScan" value="scan" checked>
                        <label class="form-check-label" for="modeScan">Scan Mode </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="entry_mode" id="modeManual" value="manual">
                        <label class="form-check-label" for="modeManual">Manual Mode </label>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="rollno" class="form-label">Roll Number / Barcode</label>
                    <input type="text" class="form-control" id="rollno" name="rollno" 
                           placeholder="Scan or type roll number" autofocus
                           onblur="handleBlur(event)">
                </div>

                <button type="submit" name="submit" class="btn btn-primary" id="submitBtn">Submit Entry</button>
            </form>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rollno'])) {
                $mode = $_POST['entry_mode'] ?? 'scan';  // default to scan
                $is_manual = ($mode === 'manual');
                echo processEntry($_POST['rollno'], $is_manual);
            }
            ?>
        </div>
    </div>
</div>

<script>
function handleBlur(event) {
    // Only auto-submit if Scan mode is selected
    const scanRadio = document.getElementById('modeScan');
    if (scanRadio.checked) {
        document.getElementById('entryForm').submit();
    }
}
</script>

<?php require('include/footer.php'); ?>