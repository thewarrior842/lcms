<?php
require('include/header.php');
require_once('config.php');

// Handle update request
if (isset($_POST['update'])) {
    // Use prepared statement
    $stmt = $con->prepare("UPDATE section_incharge SET did = ?, tid = ?, year = ?, academic_year = ? WHERE si_id = ?");
    $stmt->bind_param("iissi", $did, $tid, $year, $academic_year, $si_id);

    $si_id = intval($_POST['id']);        // form field name still 'id' for simplicity
    $did   = intval($_POST['did']);
    $tid   = intval($_POST['tid']);
    $year  = $_POST['year'];
    $academic_year = $_POST['academic_year'];

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Record updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Fetch all section incharges with department and teacher names
$sql = "SELECT si.si_id, si.did, si.tid, si.year, si.academic_year,
               d.dname, t.* 
        FROM section_incharge si
        JOIN dept d ON si.did = d.did
        JOIN teacher t ON si.tid = t.tid
        ORDER BY si.year DESC, d.dname";
$result = $con->query($sql);
?>

<div class="main-content">
    <h4>Manage Section Incharges</h4>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>SI ID</th>   <!-- changed from ID to SI ID for clarity -->
                <th>Department</th>
                <th>Teacher</th>
                <th>Year</th>
                <th>Academic Year</th>
                <th>Update</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['si_id'] ?></td>
                <td><?= htmlspecialchars($row['dname']) ?></td>
                <td><?= htmlspecialchars($row['tfname']." ".$row['tmname']." ".$row['tlname']) ?></td>
                <td><?= $row['year'] ?></td>
                <td><?= $row['academic_year'] ?></td>
                <td>
                    <!-- pass si_id to the JavaScript function -->
                    <button class="btn btn-sm btn-primary" onclick="editIncharge(<?= $row['si_id'] ?>)">Edit</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Edit Modal (unchanged except for explanatory comment) -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Section Incharge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- hidden field still named 'id' – it will hold the si_id -->
                    <input type="hidden" name="id" id="edit-id">

                    <div class="mb-3">
                        <label>Department</label>
                        <select name="did" id="edit-did" class="form-control" required>
                            <option value="">-- Select --</option>
                            <?php
                            $depts = $con->query("SELECT * FROM dept");
                            while($d = $depts->fetch_assoc()) {
                                echo "<option value='{$d['did']}'>{$d['dname']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Teacher</label>
                        <select name="tid" id="edit-tid" class="form-control" required>
                            <option value="">-- Select Teacher --</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Year</label>
                        <select name="year" id="edit-year" class="form-control" required>
                            <?php for($y = date('Y'); $y >= date('Y')-5; $y--): ?>
                            <option value="<?= $y ?>"><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Academic Year</label>
                        <select name="academic_year" id="edit-academic_year" class="form-control" required>
                            <option value="1st">1st Year</option>
                            <option value="2nd">2nd Year</option>
                            <option value="3rd">3rd Year</option>
                        </select>
                    </div>
                </div>
                <div>
                    <!-- Update botton -->
                </div>
                <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                </div> -->
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function editIncharge(si_id) {
    // Fetch current data via AJAX, passing si_id
    $.ajax({
        url: 'get_incharge.php',
        type: 'POST',
        data: {si_id: si_id},      // send si_id instead of id
        dataType: 'json',
        success: function(data) {
            // data.id is the aliased si_id from get_incharge.php
            $('#edit-id').val(data.id);
            $('#edit-did').val(data.did).trigger('change');
            $('#edit-year').val(data.year);
            $('#edit-academic_year').val(data.academic_year);

            // Wait a bit for teachers to load, then set selected teacher
            setTimeout(function() {
                $('#edit-tid').val(data.tid);
            }, 500);

            $('#editModal').modal('show');
        }
    });
}

// Load teachers when department changes (reuse fetch_incharge.php or fetch_teachers.php)
$('#edit-did').on('change', function() {
    var did = $(this).val();
    if (did) {
        $.ajax({
            url: 'fetch_incharge.php',   // or fetch_teachers.php
            type: 'POST',
            data: {did: did},
            success: function(html) {
                $('#edit-tid').html(html);
            }
        });
    } else {
        $('#edit-tid').html('<option value="">-- Select Teacher --</option>');
    }
});
</script>

<?php require('include/footer.php'); ?>