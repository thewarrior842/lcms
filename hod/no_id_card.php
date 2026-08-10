<?php
require_once('config.php');

// ========== EXPORT TO EXCEL (CSV) HANDLER ==========
if (isset($_GET['export_excel']) && $_GET['export_excel'] == '1') {
    try {
        // Get all filter parameters
        $start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
        $end_date = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';
        $search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
        $search_rollno = isset($_GET['search_rollno']) ? trim($_GET['search_rollno']) : '';
        $filter_semester = isset($_GET['filter_semester']) ? trim($_GET['filter_semester']) : '';
        $filter_department = isset($_GET['filter_department']) ? trim($_GET['filter_department']) : '';
        $min_late_count = isset($_GET['min_late_count']) ? trim($_GET['min_late_count']) : '';

        // Build subquery for "no ID card" entries aggregation per student
        $sub_sql = "SELECT n.rollno, 
                           COUNT(n.no_id) AS no_id_count,
                           MAX(n.log_date) AS last_no_id_date,
                           MAX(n.log_time) AS last_no_id_time
                    FROM no_id n
                    WHERE 1=1";

        // Apply date range filter to subquery (using log_date from no_id table)
        if (!empty($start_date) && !empty($end_date)) {
            $safe_start = $con->real_escape_string($start_date);
            $safe_end = $con->real_escape_string($end_date);
            $sub_sql .= " AND n.log_date BETWEEN '$safe_start' AND '$safe_end'";
        } elseif (!empty($start_date)) {
            $safe_start = $con->real_escape_string($start_date);
            $sub_sql .= " AND n.log_date >= '$safe_start'";
        } elseif (!empty($end_date)) {
            $safe_end = $con->real_escape_string($end_date);
            $sub_sql .= " AND n.log_date <= '$safe_end'";
        }

        $sub_sql .= " GROUP BY n.rollno";

        // Use INNER JOIN to include ONLY students who have at least one entry in the date range
        $src = "SELECT s.*, d.dname, 
                       agg.no_id_count,
                       agg.last_no_id_date,
                       agg.last_no_id_time
                FROM student s
                INNER JOIN dept d ON s.did = d.did
                INNER JOIN ($sub_sql) agg ON s.rollno = agg.rollno
                WHERE 1=1";

        // Apply filters on student table
        if (!empty($search_name)) {
            $safe_name = $con->real_escape_string($search_name);
            $src .= " AND CONCAT(s.fname, ' ', s.mname, ' ', s.lname) LIKE '%$safe_name%'";
        }
        if (!empty($search_rollno)) {
            $safe_rollno = $con->real_escape_string($search_rollno);
            $src .= " AND s.rollno LIKE '%$safe_rollno%'";
        }
        if (!empty($filter_semester)) {
            $safe_semester = $con->real_escape_string($filter_semester);
            $src .= " AND s.semester = '$safe_semester'";
        }
        if (!empty($filter_department)) {
            $safe_department = $con->real_escape_string($filter_department);
            $src .= " AND s.did = '$safe_department'";
        }

        // Apply minimum count filter
        if (!empty($min_late_count) && is_numeric($min_late_count)) {
            $safe_min = (int)$min_late_count;
            $src .= " AND agg.no_id_count >= $safe_min";
        }

        // Order by count descending (highest first), then by last date/time
        $src .= " ORDER BY agg.no_id_count DESC, agg.last_no_id_date ASC, agg.last_no_id_time ASC";

        $result = $con->query($src);
        if (!$result) {
            throw new Exception("Database query failed: " . $con->error);
        }

        // Build dynamic filename based on date range
        $filename = 'no_id_summary';
        if (!empty($start_date) && !empty($end_date)) {
            $filename .= '_' . $start_date . '_to_' . $end_date;
        } elseif (!empty($start_date)) {
            $filename .= '_from_' . $start_date;
        } elseif (!empty($end_date)) {
            $filename .= '_until_' . $end_date;
        } else {
            $filename .= '_' . date('Y-m-d');
        }
        $filename .= '.csv';

        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Column headings
        fputcsv($output, ['Serial No', 'Name', 'Roll No', 'Department', 'Last No‑ID Time', 'Last No‑ID Date', 'Semester', 'No‑ID Count']);

        // Output data rows
        $i = 1;
        while ($row = $result->fetch_assoc()) {
            $full_name = trim($row['fname'] . ' ' . ($row['mname'] ?? '') . ' ' . ($row['lname'] ?? ''));
            fputcsv($output, [
                $i++,
                $full_name,
                $row['rollno'],
                $row['dname'],
                $row['last_no_id_time'],
                $row['last_no_id_date'],
                $row['semester'],
                $row['no_id_count']   // always at least 1
            ]);
        }

        fclose($output);
        exit;
    } catch (Exception $e) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=error.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Error', $e->getMessage()]);
        fclose($output);
        exit;
    }
}

// ========== NORMAL PAGE OUTPUT CONTINUES ==========
require('include/header.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

// Fetch active departments for dropdown
$departments = [];
try {
    $dept_query = "SELECT did, dname FROM dept WHERE status='1' ORDER BY dname";
    $dept_result = $con->query($dept_query);
    if ($dept_result) {
        while ($dept_row = $dept_result->fetch_assoc()) {
            $departments[] = $dept_row;
        }
    }
} catch (Exception $e) {
    // Silently fail
}
?>
<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="header">
        <h2>No ID Card Entry</h2>
        <div class="user-info">
            <div class="user-avatar">
                <p><?php echo substr($_SESSION['h_info']['tfname'], 0, 1) . substr($_SESSION['h_info']['tmname'], 0, 1) . substr($_SESSION['h_info']['tlname'], 0, 1) ?></p>
            </div>
            <div>
                <p>Welcome back, <?php echo $_SESSION['h_info']['tfname'] . " " . $_SESSION['h_info']['tmname'] . " " . $_SESSION['h_info']['tlname']; ?>!</p>
            </div>
        </div>
    </div>

    <!-- Student List with Filters (only students with at least one no‑id entry) -->
    <div class="student-list-container">
        <div class="section-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3>Search Student</h3>
            <form method="get" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <!-- Date range inputs -->
                Start Date:
                <input type="date" name="start_date" placeholder="Start date" value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>">
                End Date:
                <input type="date" name="end_date" placeholder="End date" value="<?php echo isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : ''; ?>">

                <input type="text" name="search_name" placeholder="Search by name" value="<?php echo isset($_GET['search_name']) ? htmlspecialchars($_GET['search_name']) : ''; ?>">
                <input type="text" name="search_rollno" placeholder="Search by roll number" value="<?php echo isset($_GET['search_rollno']) ? htmlspecialchars($_GET['search_rollno']) : ''; ?>">

                <!-- Semester Dropdown (only 2, 4, 6) -->
                <select name="filter_semester">
                    <option value="">All Semesters</option>
                    <option value="2" <?php echo (isset($_GET['filter_semester']) && $_GET['filter_semester'] == '2') ? 'selected' : ''; ?>>Semester 2</option>
                    <option value="4" <?php echo (isset($_GET['filter_semester']) && $_GET['filter_semester'] == '4') ? 'selected' : ''; ?>>Semester 4</option>
                    <option value="6" <?php echo (isset($_GET['filter_semester']) && $_GET['filter_semester'] == '6') ? 'selected' : ''; ?>>Semester 6</option>
                </select>

                <!-- Department Dropdown -->
                <select name="filter_department">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?php echo $dept['did']; ?>" <?php echo (isset($_GET['filter_department']) && $_GET['filter_department'] == $dept['did']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dept['dname']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Minimum No‑ID Count filter -->
                <input type="number" name="min_late_count" placeholder="Min no‑id count" value="<?php echo isset($_GET['min_late_count']) ? htmlspecialchars($_GET['min_late_count']) : ''; ?>" min="0" style="width: 120px;">

                <button type="submit" class="btn-filter">Filter</button>
                <button type="submit" name="export_excel" value="1" class="btn-export">Export to Excel</button>

                <?php if (
                    (isset($_GET['start_date']) && $_GET['start_date'] != '') ||
                    (isset($_GET['end_date']) && $_GET['end_date'] != '') ||
                    (isset($_GET['search_name']) && $_GET['search_name'] != '') ||
                    (isset($_GET['search_rollno']) && $_GET['search_rollno'] != '') ||
                    (isset($_GET['filter_semester']) && $_GET['filter_semester'] != '') ||
                    (isset($_GET['filter_department']) && $_GET['filter_department'] != '') ||
                    (isset($_GET['min_late_count']) && $_GET['min_late_count'] != '')
                ) { ?>
                    <a href="<?php echo basename($_SERVER['PHP_SELF']); ?>" class="btn-clear">Clear Filter</a>
                <?php } ?>
            </form>
        </div>

        <?php
        // Display any message passed via URL
        if (isset($_GET['msg'])) {
            echo '<div class="alert">' . htmlspecialchars($_GET['msg']) . '</div>';
        }
        ?>

        <?php
        try {
            // Get filter parameters
            $start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
            $end_date = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';
            $search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
            $search_rollno = isset($_GET['search_rollno']) ? trim($_GET['search_rollno']) : '';
            $filter_semester = isset($_GET['filter_semester']) ? trim($_GET['filter_semester']) : '';
            $filter_department = isset($_GET['filter_department']) ? trim($_GET['filter_department']) : '';
            $min_late_count = isset($_GET['min_late_count']) ? trim($_GET['min_late_count']) : '';

            // Build subquery for "no ID card" entries aggregation per student
            $sub_sql = "SELECT n.rollno, 
                               COUNT(n.no_id) AS no_id_count,
                               MAX(n.log_date) AS last_no_id_date,
                               MAX(n.log_time) AS last_no_id_time
                        FROM no_id n
                        WHERE 1=1";

            // Apply date range filter to subquery (using log_date from no_id table)
            if (!empty($start_date) && !empty($end_date)) {
                $safe_start = $con->real_escape_string($start_date);
                $safe_end = $con->real_escape_string($end_date);
                $sub_sql .= " AND n.log_date BETWEEN '$safe_start' AND '$safe_end'";
            } elseif (!empty($start_date)) {
                $safe_start = $con->real_escape_string($start_date);
                $sub_sql .= " AND n.log_date >= '$safe_start'";
            } elseif (!empty($end_date)) {
                $safe_end = $con->real_escape_string($end_date);
                $sub_sql .= " AND n.log_date <= '$safe_end'";
            }

            $sub_sql .= " GROUP BY n.rollno";

            // Use INNER JOIN to include ONLY students who have at least one entry in the date range
            $src = "SELECT s.*, d.dname, 
                           agg.no_id_count,
                           agg.last_no_id_date,
                           agg.last_no_id_time
                    FROM student s
                    INNER JOIN dept d ON s.did = d.did
                    INNER JOIN ($sub_sql) agg ON s.rollno = agg.rollno
                    WHERE 1=1";

            // Apply filters on student table
            if (!empty($search_name)) {
                $safe_name = $con->real_escape_string($search_name);
                $src .= " AND CONCAT(s.fname, ' ', s.mname, ' ', s.lname) LIKE '%$safe_name%'";
            }
            if (!empty($search_rollno)) {
                $safe_rollno = $con->real_escape_string($search_rollno);
                $src .= " AND s.rollno LIKE '%$safe_rollno%'";
            }
            if (!empty($filter_semester)) {
                $safe_semester = $con->real_escape_string($filter_semester);
                $src .= " AND s.semester = '$safe_semester'";
            }
            if (!empty($filter_department)) {
                $safe_department = $con->real_escape_string($filter_department);
                $src .= " AND s.did = '$safe_department'";
            }

            // Apply minimum count filter
            if (!empty($min_late_count) && is_numeric($min_late_count)) {
                $safe_min = (int)$min_late_count;
                $src .= " AND agg.no_id_count >= $safe_min";
            }

            // Order by count descending (highest first), then by last date/time
            $src .= " ORDER BY agg.no_id_count DESC, agg.last_no_id_date ASC, agg.last_no_id_time ASC";

            // Execute query
            $result = $con->query($src);
            if (!$result) {
                throw new Exception("Database query failed: " . $con->error);
            }

            if (mysqli_num_rows($result) > 0) {
        ?>
                <!-- Add style for highlighting rows with no_id_count >= 3 -->
                <style>
                    .highlight-row {
                        background-color: #f8d7da !important;
                        /* light red */
                    }
                </style>
                <table class="student-table">
                    <thead>
                        <tr>
                            <th>SL No</th>
                            <th>Name</th>
                            <th>Rollno</th>
                            <th>Department</th>
                            <th>Semester</th>
                            <th>No ID Card Entry</th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody">
                        <?php
                        $i = 1;
                        while ($row = $result->fetch_assoc()) {
                            // Determine if row should be highlighted (based on no_id_count)
                            $highlight_class = ($row['no_id_count'] >= 3) ? 'class="highlight-row"' : '';
                        ?>
                            <tr <?php echo $highlight_class; ?>>
                                <td><?php echo $i; ?></td>
                                <td><?php echo htmlspecialchars($row['fname'] . ' ' . $row['mname'] . ' ' . $row['lname']); ?></td>
                                <td><?php echo htmlspecialchars($row['rollno']); ?></td>
                                <td><?php echo htmlspecialchars($row['dname']); ?></td>
                                <td><?php echo htmlspecialchars($row['semester']); ?></td>
                                <td><?php echo $row['no_id_count']; ?></td>
                            </tr>
                        <?php
                            $i++;
                        }
                        ?>
                    </tbody>
                </table>
        <?php
            } else {
                // Build descriptive message about applied filters
                $filter_msg = "";
                $filters_applied = array();
                if (!empty($start_date) && !empty($end_date)) {
                    $filters_applied[] = "date from " . htmlspecialchars($start_date) . " to " . htmlspecialchars($end_date);
                } elseif (!empty($start_date)) {
                    $filters_applied[] = "date from " . htmlspecialchars($start_date);
                } elseif (!empty($end_date)) {
                    $filters_applied[] = "date until " . htmlspecialchars($end_date);
                }
                if (!empty($search_name)) {
                    $filters_applied[] = "name containing '" . htmlspecialchars($search_name) . "'";
                }
                if (!empty($search_rollno)) {
                    $filters_applied[] = "roll number containing '" . htmlspecialchars($search_rollno) . "'";
                }
                if (!empty($filter_semester)) {
                    $filters_applied[] = "semester " . htmlspecialchars($filter_semester);
                }
                if (!empty($filter_department)) {
                    $filters_applied[] = "department ID " . htmlspecialchars($filter_department);
                }
                if (!empty($min_late_count) && is_numeric($min_late_count)) {
                    $filters_applied[] = "min no‑id count " . (int)$min_late_count;
                }
                if (!empty($filters_applied)) {
                    $filter_msg = " for " . implode(" and ", $filters_applied);
                }
                echo "<p>No Student Found" . $filter_msg . "</p>";
            }
        } catch (Exception $e) {
            echo "<div class='alert alert-error'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
        ?>
    </div>

    <?php
    require('include/footer.php');
    ?>
    </body>

    </html>