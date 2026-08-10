<?php
require_once('config.php');
header('Content-Type: application/json');

if (isset($_GET['rollno']) && !empty(trim($_GET['rollno']))) {
    $rollno = trim($_GET['rollno']);
    $stmt = $con->prepare("SELECT COUNT(*) as count FROM no_id WHERE rollno = ?");
    $stmt->bind_param("s", $rollno);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    echo json_encode(['exists' => ($row['count'] > 0)]);
    $stmt->close();
} else {
    echo json_encode(['exists' => false]);
}
?>