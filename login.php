<?php
session_start();
include "dbcon.php";

$email = $_POST['username'];
$pass = $_POST['password'];

// Prepare the SQL statement with placeholders
$sql = "SELECT * FROM (
            SELECT id, role, username, password FROM admin 
            UNION 
            SELECT id, role, username, password FROM police 
            UNION 
            SELECT id, role, username, password FROM residents 
        ) combined_table
        WHERE username = $1 AND password = $2";

// Prepare and execute the SQL statement
$result = pg_query_params($conn, $sql, array($email, $pass));

if ($result) {
    $row = pg_fetch_assoc($result);

    // Check if login is successful
    if ($row && pg_num_rows($result) === 1) {
        $_SESSION['role'] = $row['role'];
        $_SESSION['id'] = $row['id'];

        // Redirect to session.php immediately (before showing loading screen)
        header('refresh:3; url=session.php');
        exit; // Ensure no further code is executed

    } else {
        // Handle failed login
        echo "Invalid credentials.";
    }
} else {
    echo "Failed to execute query.";
}
?>
