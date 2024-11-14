<?php
include "dbcon.php";  // Assuming dbcon.php sets up the PostgreSQL connection

if (isset($_GET['category'])) {
    $category = pg_escape_string($conn, $_GET['category']);  // Use pg_escape_string for sanitization

    // PostgreSQL query with parameterized input
    $sql = "SELECT * FROM police WHERE assign = $1 AND status != 'Not Available'";

    // Execute the query with the parameter
    $result = pg_query_params($conn, $sql, array($category));

    $police = array();
    while ($row = pg_fetch_assoc($result)) {
        $police[] = $row;  // Store each row in the array
    }

    // Return JSON-encoded array
    echo json_encode($police);
}
?>
