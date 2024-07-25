<?php

include "dbcon.php";


$re = $_POST['police'];
$id = $_POST['id'];



$sql = "UPDATE reports SET police_assign='$re',finish='Unsettled' WHERE id = '$id'";
 if(mysqli_query($conn,$sql)){
 $error_message = "You Successfully asign police";
 	 		$color = "p";   
            header("Location: adminreport.php?error_message=" . $error_message . "&color=" . $color);

     

         }
?>