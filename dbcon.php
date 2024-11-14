<?php  

$sname = "dpg-csonachu0jms738mmhng-a.oregon-postgres.render.com";  // Full hostname of the PostgreSQL server
$uname = "reporting_ia98_user";         // Username
$password = "C1S8UVRh7jFTCjOkAuuV4qoZXgPfPIGG";  // Password
$db_name = "reporting_ia98";            // Database name

// Connection string for PostgreSQL
$conn = pg_connect("host=$sname dbname=$db_name user=$uname password=$password");

if (!$conn) {
    // No output or echo here
    exit; // exit silently or handle the error in login.php
}
?>
