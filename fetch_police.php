<?php
include "dbcon.php";

// Get category from URL
$category = $_GET['category'];

// PostgreSQL query
$sql = "SELECT id, fullname FROM police WHERE status != 'Not Available' AND category = $1";

// Prepare the query and bind the parameter
$result = pg_query_params($conn, $sql, array($category));

// Initialize an array to hold the police records
$police = array();

// Fetch the results
while ($row = pg_fetch_assoc($result)) {
    $police[] = $row;
}

// Output the results as JSON
echo json_encode($police);
?>
