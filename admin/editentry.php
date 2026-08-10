<?php
require('include/header.php');
require_once('config.php');

// ------------------------------
// AJAX HANDLERS (must be at top)
// ------------------------------
if (isset($_GET['ajax_check_rollno'])) {
    header('Content-Type: application/json');
    $rollno = trim($_GET['rollno'] ?? '');
    $exists = false;
    if (!empty($rollno)) {
        $stmt = $con->prepare("SELECT COUNT(*) FROM no_id WHERE rollno = ?");
        $stmt->bind_param("s", $rollno);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        $exists = ($count > 0);
    }
    echo json_encode(['exists' => $exists]);
    exit;
}

if (isset($_POST['ajax_update_noid'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';
    $rollno = trim($_POST['rollno'] ?? '');
    $log_date = $_POST['log_date'] ?? date('Y-m-d');
    $log_time = $_POST['log_time'] ?? date('H:i:s');

    if (empty($rollno)) {
        echo json_encode(['success' => false, 'message' => 'Roll number required']);
        exit;
    }

    try {
        if ($action === 'add') {
            // Check if already exists
            $stmt = $con->prepare("SELECT COUNT(*) FROM no_id WHERE rollno = ?");
            $stmt->bind_param("s", $rollno);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
            if ($count == 0) {
                $stmt = $con->prepare("INSERT INTO no_id (rollno, log_date, log_time) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $rollno, $log_date, $log_time);
                $stmt->execute();
                $stmt->close();
                echo json_encode(['success' => true, 'message' => 'Added to No ID table']);
            } else {
                echo json_encode(['success' => true, 'message' => 'Already exists']);
            }
        } elseif ($action === 'remove') {
            $stmt = $con->prepare("DELETE FROM no_id WHERE rollno = ?");
            $stmt->bind_param("s", $rollno);
            $stmt->execute();
            $stmt->close();
            echo json_encode(['success' => true, 'message' => 'Removed from No ID table']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// ------------------------------
// SESSION CHECK
// ------------------------------
if (!isset($_SESSION['a_info']['email_id'])) {
    header('Location: login.php');
    exit;
}

$error = "";
$msg = "";
$active_tab = isset($_GET['tab']) && $_GET['tab'] == 'noid' ? 'noid' : 'entry';

// ---------- ENTRY LOG ----------
$edit_id_entry = isset($_GET['edit_id_entry']) ? (int)$_GET['edit_id_entry'] : 0;
$form_data_entry = ['entry_log_id' => 0, 'rollno' => '', 'log_time' => '', 'is_late' => 0, 'log_date' => ''];

// Delete entry_log
if (isset($_POST['delete_id_entry'])) {
    $del_id = (int)$_POST['delete_id_entry'];
    try {
        $stmt = $con->prepare("DELETE FROM entry_log WHERE entry_log_id = ?");
        $stmt->bind_param("i", $del_id);
        $stmt->execute();
        $msg = $stmt->affected_rows ? "Entry deleted." : "Not found.";
        $stmt->close();
    } catch (mysqli_sql_exception $e) { $error = "Delete failed: " . $e->getMessage(); }
}

// Save entry_log
if (isset($_POST['save_entry_log'])) {
    $rollno = trim($_POST['rollno']);
    $log_time = $_POST['log_time'];
    $is_late = isset($_POST['is_late']) ? 1 : 0;
    $log_date = !empty($_POST['log_date']) ? $_POST['log_date'] : null;
    $entry_id = (int)$_POST['entry_id'];

    if (empty($rollno) || empty($log_time)) {
        $error = "Roll number and log time are required.";
    } else {
        try {
            if ($entry_id > 0) {
                $stmt = $con->prepare("UPDATE entry_log SET rollno=?, log_time=?, is_late=?, log_date=? WHERE entry_log_id=?");
                $stmt->bind_param("ssisi", $rollno, $log_time, $is_late, $log_date, $entry_id);
                $stmt->execute();
                $msg = "Entry updated.";
            } else {
                $stmt = $con->prepare("INSERT INTO entry_log (rollno, log_time, is_late, log_date) VALUES (?,?,?,?)");
                $stmt->bind_param("ssis", $rollno, $log_time, $is_late, $log_date);
                $stmt->execute();
                $msg = "Entry added.";
            }
            $stmt->close();
        } catch (mysqli_sql_exception $e) { $error = "DB error: " . $e->getMessage(); }
    }
}

// Load entry_log for editing
if ($edit_id_entry > 0) {
    $stmt = $con->prepare("SELECT * FROM entry_log WHERE entry_log_id = ?");
    $stmt->bind_param("i", $edit_id_entry);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) $form_data_entry = $row;
    $stmt->close();
}

// Filters for entry_log
$search_rollno_entry = $_GET['search_rollno_entry'] ?? '';
$date_from_entry = $_GET['date_from_entry'] ?? '';
$date_to_entry = $_GET['date_to_entry'] ?? '';
$late_only = isset($_GET['late_only']) ? (int)$_GET['late_only'] : 0;

$where = [];
$params = [];
$types = "";
if ($search_rollno_entry) { $where[] = "rollno LIKE ?"; $params[] = "%$search_rollno_entry%"; $types .= "s"; }
if ($date_from_entry) { $where[] = "log_date >= ?"; $params[] = $date_from_entry; $types .= "s"; }
if ($date_to_entry) { $where[] = "log_date <= ?"; $params[] = $date_to_entry; $types .= "s"; }
if ($late_only) { $where[] = "is_late = 1"; }
$where_sql = count($where) ? "WHERE " . implode(" AND ", $where) : "";

$entries = [];
$sql = "SELECT e.*, (SELECT COUNT(*) FROM no_id n WHERE n.rollno = e.rollno) as has_noid 
        FROM entry_log e $where_sql ORDER BY e.log_date DESC, e.log_time DESC";
$stmt = $con->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $entries[] = $row;
$stmt->close();

// ---------- NO ID TABLE ----------
$edit_id_noid = isset($_GET['edit_id_noid']) ? (int)$_GET['edit_id_noid'] : 0;
$form_data_noid = ['no_id' => 0, 'rollno' => '', 'log_date' => '', 'log_time' => ''];

// Delete no_id
if (isset($_POST['delete_id_noid'])) {
    $del_id = (int)$_POST['delete_id_noid'];
    $stmt = $con->prepare("DELETE FROM no_id WHERE no_id = ?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();
    $msg = $stmt->affected_rows ? "No ID record deleted." : "Not found.";
    $stmt->close();
}

// Save no_id
if (isset($_POST['save_no_id'])) {
    $rollno = trim($_POST['rollno_noid']);
    $log_date = $_POST['log_date_noid'];
    $log_time = $_POST['log_time_noid'];
    $noid_id = (int)$_POST['noid_id'];

    if (empty($rollno) || empty($log_date) || empty($log_time)) {
        $error = "All fields are required.";
    } else {
        try {
            if ($noid_id > 0) {
                $stmt = $con->prepare("UPDATE no_id SET rollno=?, log_date=?, log_time=? WHERE no_id=?");
                $stmt->bind_param("sssi", $rollno, $log_date, $log_time, $noid_id);
                $stmt->execute();
                $msg = "No ID record updated.";
            } else {
                $stmt = $con->prepare("INSERT INTO no_id (rollno, log_date, log_time) VALUES (?,?,?)");
                $stmt->bind_param("sss", $rollno, $log_date, $log_time);
                $stmt->execute();
                $msg = "No ID record added.";
            }
            $stmt->close();
        } catch (mysqli_sql_exception $e) { $error = "DB error: " . $e->getMessage(); }
    }
}

// Load no_id for editing
if ($edit_id_noid > 0) {
    $stmt = $con->prepare("SELECT * FROM no_id WHERE no_id = ?");
    $stmt->bind_param("i", $edit_id_noid);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) $form_data_noid = $row;
    $stmt->close();
}

// Filters for no_id
$search_rollno_noid = $_GET['search_rollno_noid'] ?? '';
$date_from_noid = $_GET['date_from_noid'] ?? '';
$date_to_noid = $_GET['date_to_noid'] ?? '';
$where_noid = []; $params_noid = []; $types_noid = "";
if ($search_rollno_noid) { $where_noid[] = "rollno LIKE ?"; $params_noid[] = "%$search_rollno_noid%"; $types_noid .= "s"; }
if ($date_from_noid) { $where_noid[] = "log_date >= ?"; $params_noid[] = $date_from_noid; $types_noid .= "s"; }
if ($date_to_noid) { $where_noid[] = "log_date <= ?"; $params_noid[] = $date_to_noid; $types_noid .= "s"; }
$where_sql_noid = count($where_noid) ? "WHERE " . implode(" AND ", $where_noid) : "";

$noid_records = [];
$sql = "SELECT * FROM no_id $where_sql_noid ORDER BY log_date DESC, log_time DESC";
$stmt = $con->prepare($sql);
if ($params_noid) $stmt->bind_param($types_noid, ...$params_noid);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $noid_records[] = $row;
$stmt->close();
?>

<!-- Main Content -->
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Entry Management</h2>
            <p class="text-muted mb-0">Welcome, <?php echo htmlspecialchars($_SESSION['a_info']['afname']); ?>!</p>
        </div>
        <div><span class="badge bg-success">Online</span></div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item"><button class="nav-link <?= $active_tab=='entry'?'active':'' ?>" data-bs-toggle="tab" data-bs-target="#entry" type="button">Entry Log</button></li>
        <li class="nav-item"><button class="nav-link <?= $active_tab=='noid'?'active':'' ?>" data-bs-toggle="tab" data-bs-target="#noid" type="button">No ID Students</button></li>
    </ul>

    <div class="tab-content mt-3">
        <!-- TAB 1: ENTRY LOG -->
        <div class="tab-pane fade show <?= $active_tab=='entry'?'active':'' ?>" id="entry">
            <!-- Filter card -->
            <div class="card mb-4">
                <div class="card-header bg-light"><strong>Filter Late Students / Entries</strong></div>
                <div class="card-body">
                    <form method="get" class="row g-3">
                        <input type="hidden" name="tab" value="entry">
                        <div class="col-md-3"><label>Roll Number</label><input type="text" name="search_rollno_entry" class="form-control" value="<?= htmlspecialchars($search_rollno_entry) ?>"></div>
                        <div class="col-md-2"><label>Date From</label><input type="date" name="date_from_entry" class="form-control" value="<?= htmlspecialchars($date_from_entry) ?>"></div>
                        <div class="col-md-2"><label>Date To</label><input type="date" name="date_to_entry" class="form-control" value="<?= htmlspecialchars($date_to_entry) ?>"></div>
                        <div class="col-md-2"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="late_only" value="1" <?= $late_only?'checked':'' ?>><label>Only late entries</label></div></div>
                        <div class="col-md-3 d-flex align-items-end"><button type="submit" class="btn btn-primary me-2">Apply</button><a href="?tab=entry" class="btn btn-secondary">Clear</a></div>
                    </form>
                </div>
            </div>

            <!-- Add/Edit Form -->
            <div class="recent-activity">
                <h5 class="mb-4"><?= $edit_id_entry ? 'Edit Entry Log' : 'Add New Entry Log' ?></h5>
                <div class="row">
                    <div class="col-md-6">
                        <form method="post" id="entry-form">
                            <input type="hidden" name="entry_id" value="<?= $form_data_entry['entry_log_id'] ?>">
                            <div class="mb-3">
                                <label>Roll Number</label>
                                <input type="text" name="rollno" id="rollno" class="form-control" required value="<?= htmlspecialchars($form_data_entry['rollno']) ?>">
                                <div id="noid-checkbox-container" class="mt-2"></div>
                            </div>
                            <div class="mb-3"><label>Log Time (HH:MM:SS)</label><input type="time" name="log_time" id="log_time" class="form-control" step="1" required value="<?= htmlspecialchars($form_data_entry['log_time']) ?>"></div>
                            <div class="mb-3 form-check"><input type="checkbox" name="is_late" class="form-check-input" value="1" <?= $form_data_entry['is_late']?'checked':'' ?>><label>Is Late</label></div>
                            <div class="mb-3"><label>Log Date</label><input type="date" name="log_date" id="log_date" class="form-control" value="<?= htmlspecialchars($form_data_entry['log_date']) ?>"></div>
                            <button type="submit" name="save_entry_log" class="btn btn-primary"><?= $edit_id_entry ? 'Update' : 'Save' ?></button>
                            <?php if ($edit_id_entry): ?><a href="?tab=entry" class="btn btn-secondary">Cancel</a><?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Entries Table -->
                <h5 class="mb-4 mt-5">Entries</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead><tr><th>ID</th><th>Roll No</th><th>Time</th><th>Late</th><th>Date</th><th>No ID Match</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php foreach ($entries as $e): ?>
                            <tr>
                                <td><?= $e['entry_log_id'] ?></td>
                                <td><?= htmlspecialchars($e['rollno']) ?></td>
                                <td><?= $e['log_time'] ?></td>
                                <td><?= $e['is_late'] ? '<span class="badge bg-danger">Late</span>' : 'On Time' ?></td>
                                <td><?= $e['log_date'] ?? '' ?></td>
                                <td><input type="checkbox" disabled <?= $e['has_noid'] ? 'checked' : '' ?>> <?= $e['has_noid'] ? 'Yes' : 'No' ?></td>
                                <td>
                                    <a href="?tab=entry&edit_id_entry=<?= $e['entry_log_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <form method="post" style="display:inline-block;" onsubmit="return confirm('Delete?');"><input type="hidden" name="delete_id_entry" value="<?= $e['entry_log_id'] ?>"><button class="btn btn-sm btn-danger">Del</button></form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($entries)): ?><td><td colspan="7" class="text-center">No entries</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: NO ID TABLE -->
        <div class="tab-pane fade show <?= $active_tab=='noid'?'active':'' ?>" id="noid">
            <!-- Filter -->
            <div class="card mb-4">
                <div class="card-header bg-light"><strong>Filter No ID Records</strong></div>
                <div class="card-body">
                    <form method="get" class="row g-3">
                        <input type="hidden" name="tab" value="noid">
                        <div class="col-md-3"><label>Roll Number</label><input type="text" name="search_rollno_noid" class="form-control" value="<?= htmlspecialchars($search_rollno_noid) ?>"></div>
                        <div class="col-md-2"><label>Date From</label><input type="date" name="date_from_noid" class="form-control" value="<?= htmlspecialchars($date_from_noid) ?>"></div>
                        <div class="col-md-2"><label>Date To</label><input type="date" name="date_to_noid" class="form-control" value="<?= htmlspecialchars($date_to_noid) ?>"></div>
                        <div class="col-md-3 d-flex align-items-end"><button type="submit" class="btn btn-primary me-2">Apply</button><a href="?tab=noid" class="btn btn-secondary">Clear</a></div>
                    </form>
                </div>
            </div>

            <!-- Add/Edit Form -->
            <div class="recent-activity">
                <h5 class="mb-4"><?= $edit_id_noid ? 'Edit No ID Record' : 'Add New No ID Record' ?></h5>
                <div class="row">
                    <div class="col-md-6">
                        <form method="post">
                            <input type="hidden" name="noid_id" value="<?= $form_data_noid['no_id'] ?>">
                            <div class="mb-3"><label>Roll Number</label><input type="text" name="rollno_noid" class="form-control" required value="<?= htmlspecialchars($form_data_noid['rollno']) ?>"></div>
                            <div class="mb-3"><label>Log Date</label><input type="date" name="log_date_noid" class="form-control" required value="<?= htmlspecialchars($form_data_noid['log_date']) ?>"></div>
                            <div class="mb-3"><label>Log Time</label><input type="time" name="log_time_noid" class="form-control" required value="<?= htmlspecialchars($form_data_noid['log_time']) ?>"></div>
                            <button type="submit" name="save_no_id" class="btn btn-primary"><?= $edit_id_noid ? 'Update' : 'Save' ?></button>
                            <?php if ($edit_id_noid): ?><a href="?tab=noid" class="btn btn-secondary">Cancel</a><?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- No ID Records Table -->
                <h5 class="mb-4 mt-5">No ID Records</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead><tr><th>No ID</th><th>Roll No</th><th>Date</th><th>Time</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php foreach ($noid_records as $r): ?>
                            <tr>
                                <td><?= $r['no_id'] ?></td>
                                <td><?= htmlspecialchars($r['rollno']) ?></td>
                                <td><?= $r['log_date'] ?></td>
                                <td><?= $r['log_time'] ?></td>
                                <td>
                                    <a href="?tab=noid&edit_id_noid=<?= $r['no_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <form method="post" style="display:inline-block;" onsubmit="return confirm('Delete?');"><input type="hidden" name="delete_id_noid" value="<?= $r['no_id'] ?>"><button class="btn btn-sm btn-danger">Del</button></form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($noid_records)): ?><tr><td colspan="5" class="text-center">No records</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Error / Success Messages -->
    <?php if($error): ?><div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if($msg): ?><div class="alert alert-success mt-3"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
</div>

<!-- jQuery + Validation + Custom AJAX -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="asstees/js/jquery.validate.js"></script>
<script>
$(document).ready(function() {
    // Form validation
    $("#entry-form").validate({
        rules: { rollno: "required", log_time: "required" },
        messages: { rollno: "Roll number required", log_time: "Log time required" }
    });

    // Function to show floating message
    function showMessage(msg, type) {
        var alertClass = (type === 'success') ? 'alert-success' : 'alert-danger';
        var toast = $('<div class="alert ' + alertClass + ' alert-dismissible fade show" style="position:fixed; top:70px; right:20px; z-index:9999;">' + msg + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
        $('body').append(toast);
        setTimeout(function() { toast.remove(); }, 3000);
    }

    // Function to update the No ID checkbox and handle toggle
    function initNoIDCheckbox(rollno, log_date, log_time, container) {
        if (!rollno.trim()) {
            container.html("");
            return;
        }
        // First check if rollno exists in no_id table
        $.ajax({
            url: window.location.pathname,
            type: "GET",
            data: { ajax_check_rollno: 1, rollno: rollno },
            dataType: "json",
            success: function(resp) {
                var exists = resp.exists;
                var html = '<div class="form-check">' +
                           '<input type="checkbox" class="form-check-input" id="noid_edit_checkbox" ' + (exists ? 'checked' : '') + '>' +
                           '<label class="form-check-label" for="noid_edit_checkbox">Mark as No ID student (add/remove from no_id table)</label>' +
                           '</div><small class="text-muted">Check to create record, uncheck to delete.</small>';
                container.html(html);
                
                // Attach change event (unbind previous to avoid duplicates)
                $("#noid_edit_checkbox").off("change").on("change", function() {
                    var action = $(this).prop("checked") ? "add" : "remove";
                    var current_date = $("#log_date").val();
                    var current_time = $("#log_time").val();
                    $.ajax({
                        url: window.location.pathname,
                        type: "POST",
                        data: {
                            ajax_update_noid: 1,
                            action: action,
                            rollno: rollno,
                            log_date: current_date,
                            log_time: current_time
                        },
                        dataType: "json",
                        success: function(res) {
                            if (res.success) {
                                showMessage(res.message, 'success');
                                // Reload page after short delay to update the table
                                setTimeout(function() { location.reload(); }, 1000);
                            } else {
                                showMessage(res.message, 'danger');
                            }
                        },
                        error: function(xhr, status, error) {
                            showMessage("AJAX error: " + error, 'danger');
                            console.log(xhr.responseText);
                        }
                    });
                });
            },
            error: function(xhr, status, error) {
                container.html('<span class="text-danger">Error checking No ID table: ' + error + '</span>');
                console.log(xhr.responseText);
            }
        });
    }

    // Trigger when rollno field loses focus
    $("#rollno").on("blur", function() {
        initNoIDCheckbox($(this).val(), $("#log_date").val(), $("#log_time").val(), $("#noid-checkbox-container"));
    });

    // Also run on page load (for editing)
    if ($("#rollno").val().trim() !== "") {
        initNoIDCheckbox($("#rollno").val(), $("#log_date").val(), $("#log_time").val(), $("#noid-checkbox-container"));
    }
});
</script>

<?php require('include/footer.php'); ?>