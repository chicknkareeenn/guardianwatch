<?php
  // session_start();
  // include('dbcon.php');
  
  // if (isset($_SESSION['type'])){
  //   header('location:session.php');
  // }
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Crime Reports web application</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="crimelogo.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Updated: Nov 17 2023 with Bootstrap v5.3.2
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>
  <nav class="navbar navbar-expand-lg " style="background-color: #184965;color: white;">
  <div class="container-fluid">
    <a class="navbar-brand" href="#" style="color: white;"> <img src="crimelogo.png" style="border-radius:50%" width="30px">GuardianWatch</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" style="color: white;" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" style="color: white;">Link</a>
        </li>
        
        
      </ul>
     
    </div>
  </div>
</nav>

 <div class="container mb-3">
        <div class="row align-items-center mt-4 mb-3">
            <div class="col-md-6 order-md-2">
                <center>
                    <img src="crimelogo.png" width="300px" class="mt-5 shadow" style="border-radius: 50%;">
                    <h4>GuardianWatch</h4>
                    <h6>Together for a Safer Tomorrow</h6>
                </center>
            </div>
            <div class="col-md-6 order-md-1">
                <div class="card mt-4 mb-3 mx-auto" style="width: 80%;">
                    <div class="card-body mt-5">
                       <div class="pt-4 pb-2 mb-3">
                    <h5 class="card-title text-center pb-0 fs-4"  style="color:#040F0F;">Login to Your Account</h5>
                  </div>
                        <form class="row g-3 needs-validation" novalidate action="login.php" method="post">


                            <div class="col-12 mb-3">
                                <label for="yourUsername" class="form-label" style="color:#040F0F;">Username</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text" id="inputGroupPrepend">@</span>
                                    <input type="text" name="username" class="form-control" id="yourUsername" required>
                                    <div class="invalid-feedback">Please enter your username.</div>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="yourPassword" class="form-label" style="color:#040F0F;">Password</label>
                                <input type="password" name="password" class="form-control" id="yourPassword" required>
                                <div class="invalid-feedback">Please enter your password!</div>
                            </div>
                            <div class="col-12 mb-3">
                                <button class="btn btn-primary w-100" type="submit" style="background-color:#184965;">Login</button>
                            </div>
                            <div class="col-12 mb-3">
                                <p class="small mb-0" style="color:#040F0F;">Don't have an account? <a href="pages-register.html">Create an account</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="card border-0">
  <div class="card-body pt-3" style="background-color: #184965;color: white;">
    <center><h2>Welcome ...</h2></center>
  </div>
</div>
  

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>