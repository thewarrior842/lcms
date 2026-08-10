<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --accent: #7209b7;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #4cc9f0;
            --warning: #f72585;
            --gray: #6c757d;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header Styles */
        .dashboard-header {
            background: white;
            border-radius: 15px;
            padding: 25px 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left h1 {
            color: var(--secondary);
            font-size: 28px;
            margin-bottom: 5px;
        }

        .header-left p {
            color: var(--gray);
            font-size: 14px;
        }

        .header-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .stats-card {
            background: linear-gradient(45deg, var(--primary), var(--accent));
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            text-align: center;
        }

        .stats-card h3 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .stats-card p {
            font-size: 12px;
            opacity: 0.9;
        }

        /* Main Content */
        .dashboard-content {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
        }

        /* Sidebar */
        .sidebar {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            height: fit-content;
        }

        .sidebar-title {
            font-size: 18px;
            color: var(--secondary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .filter-group {
            margin-bottom: 25px;
        }

        .filter-group label {
            display: block;
            color: var(--dark);
            margin-bottom: 8px;
            font-weight: 500;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .search-box {
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }

        .search-box input {
            padding-left: 45px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            font-size: 16px;
        }

        .btn-primary:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }

        /* Main Panel */
        .main-panel {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .panel-header h2 {
            color: var(--secondary);
            font-size: 22px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
        }

        .btn-icon {
            background: var(--light);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            color: var(--dark);
        }

        .btn-icon:hover {
            background: var(--primary);
            color: white;
        }

        /* Student Cards */
        .students-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .student-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            border: 1px solid #eee;
        }

        .student-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(45deg, var(--primary), var(--accent));
            color: white;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .student-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 3px solid white;
            object-fit: cover;
        }

        .student-info h3 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .student-info p {
            font-size: 13px;
            opacity: 0.9;
        }

        .card-body {
            padding: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--gray);
            font-size: 13px;
        }

        .info-value {
            font-weight: 500;
            color: var(--dark);
            font-size: 14px;
        }

        .card-footer {
            padding: 15px 20px;
            background: #f9f9f9;
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #eee;
        }

        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-active {
            background: #e7f7ef;
            color: #2ecc71;
        }

        .status-inactive {
            background: #feecf0;
            color: #e74c3c;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 15px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 30px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .modal-header h2 {
            color: var(--secondary);
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--gray);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark);
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .dashboard-content {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                order: 2;
            }
        }

        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .header-right {
                width: 100%;
                justify-content: center;
            }
            
            .students-grid {
                grid-template-columns: 1fr;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="header-left">
                <h1><i class="fas fa-graduation-cap"></i> Student Management Dashboard</h1>
                <p>Manage and monitor student information efficiently</p>
            </div>
            <div class="header-right">
                <div class="stats-card">
                    <h3 id="totalStudents">0</h3>
                    <p>Total Students</p>
                </div>
                <div class="stats-card">
                    <h3 id="activeSessions">0</h3>
                    <p>Active Sessions</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="dashboard-content">
            <!-- Sidebar -->
            <div class="sidebar">
                <h3 class="sidebar-title">Filters & Actions</h3>
                
                <div class="filter-group">
                    <label for="departmentFilter"><i class="fas fa-building"></i> Department</label>
                    <select id="departmentFilter">
                        <option value="all">All Departments</option>
                        <option value="CSE">Computer Science</option>
                        <option value="ECE">Electronics</option>
                        <option value="ME">Mechanical</option>
                        <option value="CE">Civil</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="sessionFilter"><i class="fas fa-calendar-alt"></i> Session</label>
                    <select id="sessionFilter">
                        <option value="all">All Sessions</option>
                        <option value="2023-2024">2023-2024</option>
                        <option value="2022-2023">2022-2023</option>
                        <option value="2021-2022">2021-2022</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="genderFilter"><i class="fas fa-venus-mars"></i> Gender</label>
                    <select id="genderFilter">
                        <option value="all">All Genders</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="searchBox"><i class="fas fa-search"></i> Search Student</label>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchBox" placeholder="Search by name, roll, or email...">
                    </div>
                </div>
                
                <div class="filter-group">
                    <button class="btn-primary" onclick="openAddStudentModal()">
                        <i class="fas fa-plus-circle"></i> Add New Student
                    </button>
                </div>
            </div>

            <!-- Main Panel -->
            <div class="main-panel">
                <div class="panel-header">
                    <h2><i class="fas fa-users"></i> Student Records</h2>
                    <div class="action-buttons">
                        <button class="btn-icon" onclick="refreshData()" title="Refresh">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <button class="btn-icon" onclick="exportData()" title="Export Data">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="btn-icon" onclick="printData()" title="Print">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Student Cards Container -->
                <div class="students-grid" id="studentsContainer">
                    <!-- Student cards will be dynamically generated here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Student Modal -->
    <div class="modal" id="studentModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Student</h2>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <form id="studentForm" onsubmit="saveStudent(event)">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="sid">Student ID *</label>
                        <input type="text" id="sid" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="fname">First Name *</label>
                        <input type="text" id="fname" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="lname">Last Name *</label>
                        <input type="text" id="lname" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="roll">Roll Number *</label>
                        <input type="text" id="roll" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="did">Department ID *</label>
                        <select id="did" required>
                            <option value="">Select Department</option>
                            <option value="CSE001">CSE001 - Computer Science</option>
                            <option value="ECE002">ECE002 - Electronics</option>
                            <option value="ME003">ME003 - Mechanical</option>
                            <option value="CE004">CE004 - Civil</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="fathername">Father's Name</label>
                        <input type="text" id="fathername">
                    </div>
                    
                    <div class="form-group">
                        <label for="mothername">Mother's Name</label>
                        <input type="text" id="mothername">
                    </div>
                    
                    <div class="form-group">
                        <label for="mobile">Mobile Number *</label>
                        <input type="tel" id="mobile" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email_id">Email ID *</label>
                        <input type="email" id="email_id" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="pwd">Password *</label>
                        <input type="password" id="pwd" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="gender">Gender *</label>
                        <select id="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city">
                    </div>
                    
                    <div class="form-group">
                        <label for="state">State</label>
                        <input type="text" id="state">
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="address">Address</label>
                        <textarea id="address"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="pin">PIN Code</label>
                        <input type="text" id="pin">
                    </div>
                    
                    <div class="form-group">
                        <label for="ps">Police Station</label>
                        <input type="text" id="ps">
                    </div>
                    
                    <div class="form-group">
                        <label for="dist">District</label>
                        <input type="text" id="dist">
                    </div>
                    
                    <div class="form-group">
                        <label for="session_start">Session Start *</label>
                        <input type="date" id="session_start" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="session_end">Session End *</label>
                        <input type="date" id="session_end" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="dob">Date of Birth *</label>
                        <input type="date" id="dob" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Image URL</label>
                        <input type="url" id="image" placeholder="https://example.com/image.jpg">
                    </div>
                </div>
                
                <div style="margin-top: 30px; display: flex; gap: 15px;">
                    <button type="submit" class="btn-primary">Save Student</button>
                    <button type="button" class="btn-primary" onclick="closeModal()" style="background: var(--gray);">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Sample student data
        let students = [
            {
                sid: "S001",
                fname: "John",
                lname: "Doe",
                roll: "2023001",
                did: "CSE001",
                fathername: "Robert Doe",
                mothername: "Mary Doe",
                mobile: "+91-9876543210",
                email_id: "john.doe@university.edu",
                pwd: "********",
                gender: "Male",
                city: "Mumbai",
                state: "Maharashtra",
                address: "123 Main Street, Andheri West",
                pin: "400053",
                ps: "Andheri Police Station",
                dist: "Mumbai Suburban",
                session_start: "2023-07-01",
                session_end: "2027-06-30",
                dob: "2005-03-15",
                image: "https://randomuser.me/api/portraits/men/32.jpg",
                status: "active"
            },
            {
                sid: "S002",
                fname: "Priya",
                lname: "Sharma",
                roll: "2023002",
                did: "ECE002",
                fathername: "Rajesh Sharma",
                mothername: "Sunita Sharma",
                mobile: "+91-9876543211",
                email_id: "priya.sharma@university.edu",
                pwd: "********",
                gender: "Female",
                city: "Delhi",
                state: "Delhi",
                address: "456 Park Street, Connaught Place",
                pin: "110001",
                ps: "Connaught Place PS",
                dist: "New Delhi",
                session_start: "2023-07-01",
                session_end: "2027-06-30",
                dob: "2004-11-22",
                image: "https://randomuser.me/api/portraits/women/44.jpg",
                status: "active"
            },
            {
                sid: "S003",
                fname: "Amit",
                lname: "Kumar",
                roll: "2022001",
                did: "ME003",
                fathername: "Sanjay Kumar",
                mothername: "Anita Kumar",
                mobile: "+91-9876543212",
                email_id: "amit.kumar@university.edu",
                pwd: "********",
                gender: "Male",
                city: "Bangalore",
                state: "Karnataka",
                address: "789 MG Road",
                pin: "560001",
                ps: "Ashok Nagar PS",
                dist: "Bangalore Urban",
                session_start: "2022-07-01",
                session_end: "2026-06-30",
                dob: "2003-08-10",
                image: "https://randomuser.me/api/portraits/men/67.jpg",
                status: "inactive"
            }
        ];

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            renderStudents();
            updateStats();
            
            // Add event listeners for filters
            document.getElementById('departmentFilter').addEventListener('change', filterStudents);
            document.getElementById('sessionFilter').addEventListener('change', filterStudents);
            document.getElementById('genderFilter').addEventListener('change', filterStudents);
            document.getElementById('searchBox').addEventListener('input', filterStudents);
        });

        // Render student cards
        function renderStudents(filteredStudents = students) {
            const container = document.getElementById('studentsContainer');
            container.innerHTML = '';
            
            filteredStudents.forEach(student => {
                const card = document.createElement('div');
                card.className = 'student-card';
                card.innerHTML = `
                    <div class="card-header">
                        <img src="${student.image || 'https://via.placeholder.com/70'}" alt="${student.fname}" class="student-avatar">
                        <div class="student-info">
                            <h3>${student.fname} ${student.lname}</h3>
                            <p>${student.sid} • ${student.did}</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <span class="info-label">Roll No:</span>
                            <span class="info-value">${student.roll}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value">${student.email_id}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Mobile:</span>
                            <span class="info-value">${student.mobile}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Session:</span>
                            <span class="info-value">${student.session_start} to ${student.session_end}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Location:</span>
                            <span class="info-value">${student.city}, ${student.state}</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <span class="status-badge ${student.status === 'active' ? 'status-active' : 'status-inactive'}">
                            ${student.status === 'active' ? 'Active' : 'Inactive'}
                        </span>
                        <div>
                            <button class="btn-icon" onclick="editStudent('${student.sid}')" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-icon" onclick="deleteStudent('${student.sid}')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });
        }

        // Update statistics
        function updateStats() {
            document.getElementById('totalStudents').textContent = students.length;
            
            const activeSessions = students.filter(s => 
                new Date(s.session_end) >= new Date()
            ).length;
            document.getElementById('activeSessions').textContent = activeSessions;
        }

        // Filter students based on criteria
        function filterStudents() {
            const deptFilter = document.getElementById('departmentFilter').value;
            const sessionFilter = document.getElementById('sessionFilter').value;
            const genderFilter = document.getElementById('genderFilter').value;
            const searchTerm = document.getElementById('searchBox').value.toLowerCase();
            
            let filtered = students.filter(student => {
                // Department filter
                if (deptFilter !== 'all' && !student.did.includes(deptFilter)) {
                    return false;
                }
                
                // Session filter
                if (sessionFilter !== 'all') {
                    const sessionYear = student.session_start.substring(0, 4);
                    if (sessionYear !== sessionFilter.substring(0, 4)) {
                        return false;
                    }
                }
                
                // Gender filter
                if (genderFilter !== 'all' && student.gender !== genderFilter) {
                    return false;
                }
                
                // Search filter
                if (searchTerm) {
                    const searchFields = [
                        student.fname,
                        student.lname,
                        student.roll,
                        student.email_id,
                        student.mobile,
                        student.sid
                    ];
                    if (!searchFields.some(field => 
                        field.toLowerCase().includes(searchTerm))) {
                        return false;
                    }
                }
                
                return true;
            });
            
            renderStudents(filtered);
        }

        // Modal functions
        function openAddStudentModal() {
            document.getElementById('modalTitle').textContent = 'Add New Student';
            document.getElementById('studentForm').reset();
            document.getElementById('sid').removeAttribute('readonly');
            document.getElementById('studentModal').style.display = 'flex';
        }

        function editStudent(sid) {
            const student = students.find(s => s.sid === sid);
            if (!student) return;
            
            document.getElementById('modalTitle').textContent = 'Edit Student';
            document.getElementById('sid').value = student.sid;
            document.getElementById('sid').setAttribute('readonly', 'true');
            document.getElementById('fname').value = student.fname;
            document.getElementById('lname').value = student.lname;
            document.getElementById('roll').value = student.roll;
            document.getElementById('did').value = student.did;
            document.getElementById('fathername').value = student.fathername;
            document.getElementById('mothername').value = student.mothername;
            document.getElementById('mobile').value = student.mobile;
            document.getElementById('email_id').value = student.email_id;
            document.getElementById('pwd').value = student.pwd;
            document.getElementById('gender').value = student.gender;
            document.getElementById('city').value = student.city;
            document.getElementById('state').value = student.state;
            document.getElementById('address').value = student.address;
            document.getElementById('pin').value = student.pin;
            document.getElementById('ps').value = student.ps;
            document.getElementById('dist').value = student.dist;
            document.getElementById('session_start').value = student.session_start;
            document.getElementById('session_end').value = student.session_end;
            document.getElementById('dob').value = student.dob;
            document.getElementById('image').value = student.image || '';
            
            document.getElementById('studentModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('studentModal').style.display = 'none';
        }

        // Save student (add or edit)
        function saveStudent(event) {
            event.preventDefault();
            
            const sid = document.getElementById('sid').value;
            const isEdit = students.some(s => s.sid === sid);
            
            const student = {
                sid: sid,
                fname: document.getElementById('fname').value,
                lname: document.getElementById('lname').value,
                roll: document.getElementById('roll').value,
                did: document.getElementById('did').value,
                fathername: document.getElementById('fathername').value,
                mothername: document.getElementById('mothername').value,
                mobile: document.getElementById('mobile').value,
                email_id: document.getElementById('email_id').value,
                pwd: document.getElementById('pwd').value,
                gender: document.getElementById('gender').value,
                city: document.getElementById('city').value,
                state: document.getElementById('state').value,
                address: document.getElementById('address').value,
                pin: document.getElementById('pin').value,
                ps: document.getElementById('ps').value,
                dist: document.getElementById('dist').value,
                session_start: document.getElementById('session_start').value,
                session_end: document.getElementById('session_end').value,
                dob: document.getElementById('dob').value,
                image: document.getElementById('image').value || `https://randomuser.me/api/portraits/${document.getElementById('gender').value === 'Female' ? 'women' : 'men'}/${Math.floor(Math.random() * 99)}.jpg`,
                status: 'active'
            };
            
            if (isEdit) {
                // Update existing student
                const index = students.findIndex(s => s.sid === sid);
                students[index] = student;
            } else {
                // Add new student
                students.push(student);
            }
            
            closeModal();
            renderStudents();
            updateStats();
            filterStudents();
            
            alert(`Student ${isEdit ? 'updated' : 'added'} successfully!`);
        }

        // Delete student
        function deleteStudent(sid) {
            if (confirm('Are you sure you want to delete this student?')) {
                students = students.filter(s => s.sid !== sid);
                renderStudents();
                updateStats();
                alert('Student deleted successfully!');
            }
        }

        // Utility functions
        function refreshData() {
            renderStudents();
            updateStats();
            alert('Data refreshed!');
        }

        function exportData() {
            const dataStr = JSON.stringify(students, null, 2);
            const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
            
            const exportFileDefaultName = 'students_data.json';
            
            const linkElement = document.createElement('a');
            linkElement.setAttribute('href', dataUri);
            linkElement.setAttribute('download', exportFileDefaultName);
            linkElement.click();
        }

        function printData() {
            window.print();
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('studentModal');
            if (event.target === modal) {
                closeModal();
            }
        };
    </script>
</body>
</html>