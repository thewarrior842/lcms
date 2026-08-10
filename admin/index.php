<?php require('include/header.php') ?>
<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Dashboard</h2>
            <p class="text-muted mb-0">Welcome back, <?php echo $_SESSION['a_info']['afname'] . " " . $_SESSION['a_info']['alname']; ?>!</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="user-avatar me-3">
                <!-- <?php echo strtoupper(substr($_SESSION['a_info']['afname'], 0, 1)); ?> -->
            </div>
            <div>
                <span class="badge bg-success">Online</span>
            </div>
        </div>
    </div>

    <?php
    // --- MONTH FILTER LOGIC ---
    $availableMonths = [];
    $monthQuery = "SELECT DISTINCT DATE_FORMAT(log_date, '%Y-%m') as month_val 
                   FROM entry_log ORDER BY month_val DESC";
    $monthRes = $con->query($monthQuery);
    while ($row = $monthRes->fetch_assoc()) {
        $availableMonths[] = $row['month_val'];
    }

    $selectedMonth = isset($_GET['month']) ? $_GET['month'] : 
                     (count($availableMonths) > 0 ? $availableMonths[0] : date('Y-m'));

    // --- 1. Get all departments ---
    $deptQuery = "SELECT did, dname FROM dept ORDER BY did";
    $deptResult = $con->query($deptQuery);
    $departments = [];
    $deptNames = [];
    while ($row = $deptResult->fetch_assoc()) {
        $departments[] = $row['did'];
        $deptNames[$row['did']] = $row['dname'];
    }

    // --- 2. Total students per department (all semesters) ---
    $totalStudentsQuery = "
        SELECT did, COUNT(sid) as total
        FROM student
        GROUP BY did
    ";
    $totalStuRes = $con->query($totalStudentsQuery);
    $totalStudents = [];
    while ($row = $totalStuRes->fetch_assoc()) {
        $totalStudents[$row['did']] = (int)$row['total'];
    }

    // --- 3. Late students per department (selected month) ---
    $lateQuery = "
        SELECT s.did, COUNT(DISTINCT s.sid) AS late
        FROM student s
        INNER JOIN entry_log l ON s.rollno = l.rollno
        WHERE l.is_late = 1 AND DATE_FORMAT(l.log_date, '%Y-%m') = ?
        GROUP BY s.did
    ";
    $stmt = $con->prepare($lateQuery);
    $stmt->bind_param("s", $selectedMonth);
    $stmt->execute();
    $lateResult = $stmt->get_result();
    $lates = [];
    while ($row = $lateResult->fetch_assoc()) {
        $lates[$row['did']] = (int)$row['late'];
    }
    $stmt->close();
    ?>

    <!-- Month Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-auto">
                    <label for="month" class="form-label">Select Month</label>
                    <select name="month" id="month" class="form-select" onchange="this.form.submit()">
                        <?php foreach ($availableMonths as $month): ?>
                            <option value="<?php echo $month; ?>" <?php echo ($month == $selectedMonth) ? 'selected' : ''; ?>>
                                <?php echo date('F Y', strtotime($month . '-01')); ?>
                            </option>
                        <?php endforeach; ?>
                        <?php if (empty($availableMonths)): ?>
                            <option value="">No log data available</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-auto">
                   
                </div>
            </form>
        </div>
    </div>

    <h1>Late Comers per Department (<?php echo date('F Y', strtotime($selectedMonth . '-01')); ?>)</h1>

    <style>
        .charts-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-bottom: 30px;
        }
        .chart-container {
            flex: 0 0 auto;
            width: 300px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .chart-container h2 {
            font-size: 1.2em;
            margin: 0 0 10px 0;
            color: #333;
        }
        canvas {
            max-width: 100%;
            height: auto;
        }
        .no-data-message {
            color: #999;
            font-style: italic;
            padding: 20px 0;
        }
    </style>

    <!-- Pie Charts Row (based on total students, late vs not late) -->
    <div class="charts-row">
        <?php foreach ($departments as $dept): 
            $deptName = $deptNames[$dept];
            $total = isset($totalStudents[$dept]) ? $totalStudents[$dept] : 0;
            $late = isset($lates[$dept]) ? $lates[$dept] : 0;
            $notLate = $total - $late;
        ?>
            <div class="chart-container">
                <h2><?php echo htmlspecialchars($deptName); ?></h2>
                <?php if ($total == 0): ?>
                    <div class="no-data-message">No students</div>
                <?php else: ?>
                    <canvas id="chart-<?php echo $dept; ?>" width="300" height="300"></canvas>
                    <script>
                        (function() {
                            var ctx = document.getElementById('chart-<?php echo $dept; ?>').getContext('2d');
                            var late = <?php echo $late; ?>;
                            var notLate = <?php echo $notLate; ?>;

                            var chartData = [];
                            var chartLabels = [];
                            var chartBackgroundColors = [];
                            var chartBorderColors = [];

                            if (late > 0) {
                                chartData.push(late);
                                chartLabels.push('Late');
                                chartBackgroundColors.push('#f50109'); // red
                                chartBorderColors.push('#cc2f3c');
                            }
                            if (notLate > 0) {
                                chartData.push(notLate);
                                chartLabels.push('Not Late');
                                chartBackgroundColors.push('#42eb36'); // green
                                chartBorderColors.push('#2cb856');
                            }

                            if (chartData.length === 0) return;

                            new Chart(ctx, {
                                type: 'pie',
                                data: {
                                    labels: chartLabels,
                                    datasets: [{
                                        data: chartData,
                                        backgroundColor: chartBackgroundColors,
                                        borderColor: chartBorderColors,
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: true,
                                    plugins: {
                                        legend: { display: true, position: 'bottom' },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    let label = context.label || '';
                                                    let value = context.raw;
                                                    let total = <?php echo $total; ?>;
                                                    let percentage = ((value / total) * 100).toFixed(1);
                                                    return `${label}: ${value} (${percentage}%)`;
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        })();
                    </script>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Summary Table: Total Students, Late Students, Remaining (Total - Late), % Late -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Department</th>
                <th>Total Students</th>
                <th>Late Students (<?php echo date('F Y', strtotime($selectedMonth . '-01')); ?>)</th>
                <th>Remaining (Total - Late)</th>
                <th>% Late (of total students)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($departments as $dept): 
                $deptName = $deptNames[$dept];
                $total = isset($totalStudents[$dept]) ? $totalStudents[$dept] : 0;
                $late = isset($lates[$dept]) ? $lates[$dept] : 0;
                $remaining = $total - $late;
                $percentLate = ($total > 0) ? round(($late / $total) * 100, 1) . '%' : '0%';
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($deptName); ?></td>
                    <td><?php echo $total; ?></td>
                    <td><?php echo $late; ?></td>
                    <td><?php echo $remaining; ?></td>
                    <td><?php echo $percentLate; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require('include/footer.php') ?>