<?php
session_start();
include "dbcon.php";
$email = $_POST['username'];
$pass = $_POST['password'];
$sql = "SELECT * FROM ( 
 			SELECT id, role, username, password FROM admin 
 			UNION 
 			SELECT id, role, username, password FROM police 
 			UNION 
 			SELECT id, role, username, password FROM residents 
 		) combined_table
        WHERE username = ? AND password = ?";
        $stmt = mysqli_prepare($conn, $sql);
	if ($stmt) {
	    mysqli_stmt_bind_param($stmt, "ss", $email, $pass);
	    mysqli_stmt_execute($stmt);
	    $result = mysqli_stmt_get_result($stmt);
	    $row = mysqli_fetch_assoc($result);
	    if ($row && mysqli_num_rows($result) === 1) {
	    		$_SESSION['role'] = $row['role'];
            	$_SESSION['id'] = $row['id'];
	    		header('refresh:1; url=session.php');
	    }
	    else{
	    	echo "wala";
	    }
	}
?>