<?php

include "dbcon.php";

// Get the POST data
$re = $_POST['reason'];
$id = $_POST['id'];

// Create the PostgreSQL query
$sql = "UPDATE reports SET reason = $1, finish = 'Reject' WHERE id = $2";

// Prepare the query
$result = pg_query_params($conn, $sql, array($re, $id));

// Check if the query was successful
if ($result) {
    $error_message = "You Rejected report";
    $color = "r";   
    header("Location: adminreport.php?error_message=" . urlencode($error_message) . "&color=" . $color);
} else {
    // If the query fails, show an error
    echo "Error: " . pg_last_error($conn);
}

?>
