<?php
require_once('include/header.php');
?>
<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="header">
        <h2>Teacher/Faculty Dashboard</h2>
        <div class="user-info">
            <div class="user-avatar">
                <p><?php echo substr($_SESSION['h_info']['tfname'], 0, 1) . substr($_SESSION['h_info']['tmname'], 0, 1). substr($_SESSION['h_info']['tlname'], 0, 1) ?></p>
            </div>
            <div>
                <p>Welcome back, <?php echo $_SESSION['h_info']['tfname'] . " " . $_SESSION['h_info']['tmname']. " " . $_SESSION['h_info']['tlname']; ?>!</p>
            </div>
        </div>
    </div>

    <div>
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
        $departments = [];        // list of department ids
        $deptNames = [];          // mapping did -> dname
        while ($row = $deptResult->fetch_assoc()) {
            $departments[] = $row['did'];
            $deptNames[$row['did']] = $row['dname'];
        }

        // --- 2. Total students per department (all students) ---
        $totalQuery = "
            SELECT did, COUNT(sid) AS total
            FROM student
            GROUP BY did
        ";
        $totalResult = $con->query($totalQuery);
        $totals = [];
        while ($row = $totalResult->fetch_assoc()) {
            $totals[$row['did']] = (int)$row['total'];
        }

        // --- 3. Late students per department for the selected month ---
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

        // Close connection if not needed later (footer may need it, so comment out)
        // $con->close();

        // --- 4. Build chart data array (total students & late students per department) ---
        $chartData = [];
        foreach ($departments as $dept) {
            $total = isset($totals[$dept]) ? $totals[$dept] : 0;
            $late = isset($lates[$dept]) ? $lates[$dept] : 0;
            $chartData[$dept] = [
                'name'  => $deptNames[$dept],
                'total' => $total,
                'late'  => $late
            ];
        }
        ?>

        <!-- Month Filter Form -->
        <div class="card mb-4" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-auto">
                    <label for="month" class="form-label"><strong>Select Month</strong></label>
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

        <h2>Late Comers Graph per Department (<?php echo date('F Y', strtotime($selectedMonth . '-01')); ?>)</h2>

        <!-- Flex container for charts -->
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
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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

        <div class="charts-row">
            <?php foreach ($chartData as $dept => $data): 
                $total = $data['total'];
                $late = $data['late'];
                $notLate = $total - $late;
            ?>
                <div class="chart-container">
                    <h2><?php echo htmlspecialchars($data['name']); ?></h2>
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
                                    chartBackgroundColors.push('#f50109');
                                    chartBorderColors.push('#cc2f3c');
                                }
                                if (notLate > 0) {
                                    chartData.push(notLate);
                                    chartLabels.push('Not Late');
                                    chartBackgroundColors.push('#42eb36');
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
                                            legend: {
                                                display: true,
                                                position: 'bottom'
                                            },
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

        <!-- Summary Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Department</th>
                    <th>Total Students</th>
                    <th>Late Students (<?php echo date('F Y', strtotime($selectedMonth . '-01')); ?>)</th>
                    <th>Remaining </th>
                    <th>% Late (of total students)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($chartData as $data): 
                    $total = $data['total'];
                    $late = $data['late'];
                    $remaining = $total - $late;
                    $percentLate = ($total > 0) ? round(($late / $total) * 100, 1) . '%' : '0%';
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($data['name']); ?></td>
                        <td><?php echo $total; ?></td>
                        <td><?php echo $late; ?></td>
                        <td><?php echo $remaining; ?></td>
                        <td><?php echo $percentLate; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    require_once('include/footer.php');
    ?>