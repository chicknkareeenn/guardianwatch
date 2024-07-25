<?php
session_start();
include "dbcon.php";
if($_SESSION['role'] == "admin"){
        header('refresh:1; url= adminmain.php');
}
else if($_SESSION['role']=="user"){
          header('refresh:1; url= usermain.php');
}
else if($_SESSION['role']=="police"){
    header('refresh:1; url= policemain.php');

}

?>