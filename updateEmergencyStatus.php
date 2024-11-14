<?php
session_start();
include "dbcon.php";

// Check session role
if (!isset($_SESSION['role']) || (trim($_SESSION['role']) == '')) {
    header('location:main.php');
    exit();
}

// Get data from the AJAX request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Prepare an update statement with parameterized query
    $sql = "UPDATE emergency SET status = $1 WHERE id = $2";
    $result = pg_query_params($conn, $sql, array($status, $id));

    // Check if the query was successful
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }

    // Close the connection
    pg_close($conn);
}
?>
