<?php
session_start();
include "dbcon.php";  // Assuming dbcon.php sets up the PostgreSQL connection

if (!isset($_SESSION['role']) || (trim($_SESSION['role']) == '')) {
    header('location:main.php');
    exit();
}

$reportId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// PostgreSQL query with parameterized input
$sql = "SELECT * FROM files WHERE report_id = $1";

// Execute the query with the parameter
$result = pg_query_params($conn, $sql, array($reportId));

$files = [];
while ($row = pg_fetch_assoc($result)) {
    $files[] = [
        'name' => $row['file_name'],
        'url' => 'uploads/' . $row['file_name'] // Adjust the path to your file location
    ];
}

header('Content-Type: application/json');
echo json_encode(['files' => $files]);
?>
