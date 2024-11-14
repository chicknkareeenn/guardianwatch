<?php
include "dbcon.php";

// Get data from POST
$re = $_POST['police'];
$id = $_POST['id'];

// Prepare the query to update the report
$sql = "UPDATE reports SET police_assign = $1, finish = 'Unsettled' WHERE id = $2";

// Prepare the statement and bind parameters
$result = pg_query_params($conn, $sql, array($re, $id));

if ($result) {
    $error_message = "You Successfully assigned police";
    $color = "p";   
    header("Location: adminreport.php?error_message=" . $error_message . "&color=" . $color);
} else {
    // Handle error if the query fails
    echo "Error: " . pg_last_error($conn);
}
?>
