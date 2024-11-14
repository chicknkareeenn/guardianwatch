<?php
session_start();
include "dbcon.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);

    // PostgreSQL query with placeholders
    $sql = "UPDATE reports SET finish = 'Closed' WHERE id = $1";

    // Use pg_query_params for executing the query with parameter binding
    $result = pg_query_params($conn, $sql, array($id));

    if ($result) {
        echo 'success';
    } else {
        // Handle error if query fails
        echo 'error: ' . pg_last_error($conn);
    }

    // No need for closing statements like in mysqli, just closing the connection
    pg_close($conn);
}
?>
