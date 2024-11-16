<?php 
 session_start();
   include "dbcon.php";
  if (!isset($_SESSION['role']) ||(trim ($_SESSION['role']) == '')) {
        header('location:main.php');
        exit();
    }  ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Guardian Watch</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="logo.png" rel="icon">
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

 
  <style type="text/css">
    .error-message {
            height: 10px; /* Fixed height for the error message container */
            margin-bottom: 10px;
            margin-top: 10px;

        }
  </style>
</head>

<body>
  <?php
    include "nav1.php";
  ?>
  
  

  <main id="main" class="main">

    <div class="pagetitle">
    <h1>
    Police List 
    <div class="float-end">
        <button class="btn btn-sm" style="background-color: #184965; color: white;" data-bs-toggle="modal" data-bs-target="#exampleModal">
            <i class="bi bi-plus-lg"></i> Create New Police Account
        </button>
    </div>
</h1>
      <nav>
       
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
              <div class="card recent-sales overflow-auto">

                <div class="card-body">
                  <div class="error-message">
                  <?php
                    if (!empty($_GET['error_message'])) {
                      $co = isset($_GET['color']) ? $_GET['color'] : 'p';
                      if ($co == "p") {
                        echo "<div class='alert alert-primary' style='text-align:center;' role='alert'> " . $_GET['error_message'] . "</div>";
                      } else {
                        echo "<div class='alert alert-danger' style='text-align:center;' role='alert'> " . $_GET['error_message'] . "</div>";
                      }
                    }
                    ?>
                    </div>
                </div>
                <div class="card-body mt-3">

                  <!-- <h5 class="card-title">Landlord List</h5> -->
                  

                  <table class="table table-borderless datatable mt-2" id="data-table">
                    <thead>
                      <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Image</th>
                        <th scope="col">Username</th>
                        <th scope="col">Contact</th>
                 
                        <th scope="col">Rank</th>
                    
                        <th scope="col">Status</th>

                      
                      </tr>
                    </thead>
                    <tbody>
                    <?php
                // Database query using PostgreSQL
                $sql = "SELECT * FROM police";
                $result = pg_query($conn, $sql); // pg_query for PostgreSQL
                while ($row = pg_fetch_assoc($result)) {
                  echo "<tr>";
                  echo "<td>".$row['fullname']."</td>";
                  echo "<td><img src='https://raw.githubusercontent.com/chicknkareeenn/guardianwatch/master/upload/".$row['image']."' width='50px' style='border-radius:50%;' data-bs-toggle='modal' data-bs-target='#pic".$row['id']."'></td>";
                  echo "<td>".$row['username']."</td>";
                  echo "<td>".$row['contact']."</td>";
                  echo "<td>".$row['ran']."</td>";
                  echo "<td>".$row['status']."</td>";
                  echo "</tr>";
                }
                ?>
                    
                      
                    </tbody>
                  </table>

                </div>

              </div>
            </div><!-- End Recent Sales -->

    </section>

  </main>




<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Police Account</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="addnewpolice.php" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="fullname" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="fullname" name="fullname" required>
          </div>
        <div class="mb-3">
            <label for="username" class="form-label">Email Address</label>
            <input type="text" class="form-control" id="username" name="email" required>
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
            <label for="ran" class="form-label">Rank</label>
            <input type="text" class="form-control" id="ran" name="ran" required>
          </div>
          <div class="mb-3">
            <label for="image" class="form-label">Profile Image</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
          </div>
          <div class="mb-3">
            <label for="contact" class="form-label">Contact No.</label>
            <input type="text" class="form-control" id="contact" name="contact" required>
          </div>
         
          <div class="mb-3">
            <label for="contact" class="form-label">Assigned Category</label>
            <select class="form-select" aria-label="Default select example" name="category">
                          <option selected>Open this select menu</option>
                          <?php
    // PostgreSQL connection
                              // PostgreSQL query
                              $sql = "SELECT * FROM categories";
                              $result = pg_query($conn, $sql);

                              if (!$result) {
                                  echo "An error occurred while executing the query.";
                                  exit;
                              }

                              // Fetch and display the options
                              while ($row = pg_fetch_assoc($result)) {
                                  echo "<option value='" . $row['id'] . "'>" . $row['category'] . "</option>";
                              }
                          ?>

                        </select>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn " style="background-color:#184965;color: white;">Add New</button>
        </form>
      </div>
    </div>
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


