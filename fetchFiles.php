<?php
session_start();
include "dbcon.php";

if (!isset($_SESSION['role']) || (trim($_SESSION['role']) == '')) {
    header('location:main.php');
    exit();
}

$reportId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT * FROM files WHERE report_id = $reportId";
$result = mysqli_query($conn, $sql);

$files = [];
while ($row = mysqli_fetch_assoc($result)) {
    $files[] = [
        'name' => $row['file_name'],
        'url' => 'uploads/' . $row['file_name'] // Adjust the path to your file location
    ];
}

header('Content-Type: application/json');
echo json_encode(['files' => $files]);
?>
