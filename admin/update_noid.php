<?php
require_once('config.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$action = $_POST['action'] ?? '';
$rollno = trim($_POST['rollno'] ?? '');
$log_date = $_POST['log_date'] ?? null;
$log_time = $_POST['log_time'] ?? null;

if (empty($rollno)) {
    echo json_encode(['success' => false, 'message' => 'Roll number is required']);
    exit;
}

try {
    if ($action === 'add') {
        // Check if already exists to avoid duplicate
        $stmt = $con->prepare("SELECT COUNT(*) FROM no_id WHERE rollno = ?");
        $stmt->bind_param("s", $rollno);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        
        if ($count == 0) {
            // Insert with provided log_date and log_time (or use current date/time if not provided)
            if (empty($log_date)) $log_date = date('Y-m-d');
            if (empty($log_time)) $log_time = date('H:i:s');
            $stmt = $con->prepare("INSERT INTO no_id (rollno, log_date, log_time) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $rollno, $log_date, $log_time);
            $stmt->execute();
            $stmt->close();
            echo json_encode(['success' => true, 'message' => 'No ID record added']);
        } else {
            echo json_encode(['success' => true, 'message' => 'Record already exists']);
        }
    } 
    elseif ($action === 'remove') {
        $stmt = $con->prepare("DELETE FROM no_id WHERE rollno = ?");
        $stmt->bind_param("s", $rollno);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        if ($affected > 0) {
            echo json_encode(['success' => true, 'message' => 'No ID record removed']);
        } else {
            echo json_encode(['success' => true, 'message' => 'No record found to delete']);
        }
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>