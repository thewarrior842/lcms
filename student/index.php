<?php require_once('include/header.php'); ?>
<?php
$rollno = $_SESSION['s_info']['rollno'];

$query = $con->prepare("
SELECT 
e.log_date,
e.log_time,
CASE 
 WHEN a.status IS NULL THEN 'PRESENT'
 ELSE a.status
END AS status,
CASE 
 WHEN e.is_late = 1 THEN 'Late Entry'
 ELSE 'On Time'
END AS remarks
FROM entry_log e
LEFT JOIN attendance a 
ON a.rollno=e.rollno 
AND a.date=e.log_date
WHERE e.rollno=?
ORDER BY e.log_date DESC
");

$query->bind_param("s",$rollno);
$query->execute();
$result = $query->get_result();
?>

<?php
$monthQuery = $con->prepare("
SELECT DISTINCT 
DATE_FORMAT(log_date,'%M') AS month_name,
DATE_FORMAT(log_date,'%m') AS month_no
FROM entry_log
WHERE rollno=?
ORDER BY month_no DESC
");

$monthQuery->bind_param("s",$rollno);
$monthQuery->execute();
$months = $monthQuery->get_result();
?>
<?php
$progressQuery = $con->prepare("
SELECT 
DATE_FORMAT(MIN(e.log_date),'%M') AS month_name,
MONTH(e.log_date) AS month_no,
YEAR(e.log_date) AS year_no,
COUNT(e.entry_log_id) AS total_days,
COUNT(e.entry_log_id) -
SUM(CASE WHEN a.status='ABSENT' THEN 1 ELSE 0 END) AS present_days
FROM entry_log e
LEFT JOIN attendance a 
ON a.rollno = e.rollno 
AND a.date = e.log_date
WHERE e.rollno = ?
GROUP BY YEAR(e.log_date), MONTH(e.log_date)
ORDER BY year_no DESC, month_no DESC
");

$progressQuery->bind_param("s",$rollno);
$progressQuery->execute();
$progressResult = $progressQuery->get_result();
?>


<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="header">
        <h2>Attendance Dashboard</h2>
    </div>

    <div class="attendance-progress-container">
        <h3>Monthly Attendance Progress</h3>

        <?php while($row=$progressResult->fetch_assoc()){ 
            $percent = ($row['total_days']>0) 
                ? round(($row['present_days']/$row['total_days'])*100)
                : 0;
        ?>
        
        <div class="progress-item">
            <div class="progress-label">
                <?php echo $row['month_name']; ?> (<?php echo $percent; ?>%)
            </div>

            <div class="progress-bar">
                <div class="progress-fill" 
                    style="width: <?php echo $percent; ?>%;">
                </div>
            </div>
        </div>

        <?php } ?>
    </div>


    <!-- Attendance Table -->
    <div class="attendance-table-container">
        <div class="table-header">
            <h3>Detailed Attendance Record</h3>
            <div class="subject-filter">
                <div class="subject-btn active">All</div>
                <?php while($m=$months->fetch_assoc()){ ?>
                <div class="subject-btn"
                    data-month="<?php echo $m['month_no']; ?>">
                    <?php echo $m['month_name']; ?>
                </div>
                <?php } ?>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Remarks</th>
                </tr>
            </thead>

            <tbody id="attendanceTableBody">
                <?php while($row=$result->fetch_assoc()){ ?>
                <tr>
                    <td><?php echo $row['log_date']; ?></td>
                    <td><?php echo $row['log_time']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td><?php echo $row['remarks']; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php require('include/footer.php') ?>