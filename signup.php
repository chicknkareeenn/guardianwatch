<?php
  session_start();
  include('dbcon.php');
  
  if (isset($_SESSION['type'])) {
    header('location:session.php');
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signup Page</title>
  
  <!-- Favicons -->
  <link href="crimelogo.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600|Poppins:400,500,600" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

  <!-- Custom CSS for styling -->
  <style>
    body {
      background-color: #f0f2f5;
      font-family: 'Open Sans', sans-serif;
    }
    .container {
      margin-top: 50px;
    }
    .card {
      border-radius: 10px;
    }
    .card-body {
      padding: 40px;
    }
    h2 {
      font-family: 'Poppins', sans-serif;
      font-weight: 600;
      color: #333;
    }
    .form-label {
      font-weight: 500;
    }
    .btn-primary {
      background-color: #184965;
      border-color: #184965;
    }
    .btn-primary:hover {
      background-color: #455c63;
      border-color: #455c63;
    }
    .btn-secondary {
      background-color: grey;
      border-color: grey;
    }
    .error-message {
      margin-bottom: 10px;
      text-align: center;
    }
    .alert-primary {
      background-color: #d1ecf1;
      color: #0c5460;
    }
    .alert-danger {
      background-color: #f8d7da;
      color: #721c24;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow border-0">
          <div class="card-body">
            <!-- Error Messages -->
            <div class="error-message">
              <?php
              if (!empty($_GET['error_message'])) {
                $co = isset($_GET['color']) ? $_GET['color'] : 'p';
                if ($co == "p") {
                  echo "<div class='alert alert-primary' role='alert'> " . $_GET['error_message'] . "</div>";
                } else {
                  echo "<div class='alert alert-danger' role='alert'> " . $_GET['error_message'] . "</div>";
                }
              }
              ?>
            </div>

            <!-- Signup Form -->
            <h2 class="text-center mb-4">Create an Account</h2>
            <form action="saveacc.php" method="post" enctype='multipart/form-data'>
              <div class="mb-3">
                <label for="first_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
              </div>
              <div class="mb-3">
                <label for="address" class="form-label">Email</label>
                <input type="email" class="form-control" id="address" name="address" required>
              </div>
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
              </div>
              <div class="mb-3">
                <label for="image" class="form-label">Profile Image</label>
                <input type="file" class="form-control" id="image" accept="image/*" name="image">
              </div>
              <div class="mb-3">
                <label for="mobile" class="form-label">Mobile Number</label>
                <input type="tel" class="form-control" id="mobile" name="mobile" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Signup</button>
              <br><br>
              <a href="index.php" class="btn btn-secondary w-100">Back</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Scroll to top button -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
