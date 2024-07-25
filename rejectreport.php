<?php

include "dbcon.php";


$re = $_POST['reason'];
$id = $_POST['id'];



$sql = "UPDATE reports SET reason='$re',finish='Reject' WHERE id = '$id'";
 if(mysqli_query($conn,$sql)){
 $error_message = "You Rejected report";
 	 		$color = "r";   
            header("Location: adminreport.php?error_message=" . $error_message . "&color=" . $color);

     

         }
?>