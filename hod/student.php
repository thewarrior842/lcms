<?php
require('include/header.php');
require_once('config.php');
?>
<?php
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
    unset($_SESSION['success']);
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

    <!-- Stats Cards -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--secondary);">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>1,254</h3>
                <p>Total Students</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--success);">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-info">
                <h3>86</h3>
                <p>Faculty Members</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--warning);">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-info">
                <h3>42</h3>
                <p>Active Courses</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background-color: var(--accent);">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-info">
                <h3>94%</h3>
                <p>Attendance Rate</p>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="recent-activity">
        <!-- Main Form -->
        <form id="settingsForm">
            <div class="form-header">
                <h5>Personal Information</h5>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fname" class="form-label">Enter Student First Name </label>
                        <input type="text" class="form-control" id="fname" value="" >
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="mname" class="form-label">Enter Student Middle Name </label>
                        <input type="text" class="form-control" id="mname" value="" >
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="lname" class="form-label">Enter Student Last Name </label>
                        <input type="text" class="form-control" id="lname" value="" >
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="mobile" class="form-label">Enter Student Phone Number</label>
                        <input type="number" class="form-control" id="mobile" value="">
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="department" class="form-label">Enter Student Department</label>
                        <select class="form-select" id="department">
                            <option>#</option>
                            <option>#</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="role" class="form-label">Department</label>
                        <select class="form-select" id="role" >
                            <option selected>#</option>
                            <option>#</option>
                            <option>#</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <button type="button" class="btn" id="cancelBtn">
                    <i class="fas fa-times me-2"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Save Changes
                </button>
            </div>
        </form>
    </div>

    <?php
    require('include/footer.php');

    ?>

    <script>
        // Form submission handler
        document.getElementById('settingsForm').addEventListener('submit', function(e) {
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
        document.getElementById('cancelBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
                // Reset form to original values
                document.getElementById('settingsForm').reset();
                document.getElementById('successMessage').style.display = 'none';
            }
        });

        // Form validation
        const form = document.getElementById('settingsForm');
        const requiredFields = form.querySelectorAll('[required]');

        form.addEventListener('submit', function(e) {
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
    </script>
    </body>

    </html>