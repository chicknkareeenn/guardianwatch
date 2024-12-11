<?php 
session_start();
include "dbcon.php";
if (!isset($_SESSION['role']) || (trim($_SESSION['role']) == '')) {
    header('location:main.php');
    exit();
}
?>

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
      <h1>On Going Reports</h1>
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
                  if($co == "p"){
                    echo "<div class='alert alert-primary' style='text-align:center;' role='alert'>" . $_GET['error_message'] . "</div>";
                  } else {
                    echo "<div class='alert alert-danger' style='text-align:center;' role='alert'>" . $_GET['error_message'] . "</div>";
                  }
                }
              ?>
            </div>
          </div>
          <div class="card-body mt-3">
            <table class="table table-borderless datatable mt-2" id="data-table">
              <thead>
                <tr>
                  <th scope="col">User Reported</th>
                  <th scope="col">Victim Name</th>
                  <th scope="col">Case</th>
                  <th scope="col">Details</th>
                  <th scope="col">Witness Name</th>
                  <th scope="col">File Date</th>
                  <th scope="col">Police Assign</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
              <?php
                  // PostgreSQL query
                  $sql = "SELECT r.id, r.name, r.witness, u.fullname AS user, p.fullname AS police, r.category, r.description, r.file_date, r.finish 
                          FROM reports AS r 
                          INNER JOIN police AS p ON r.police_assign = p.id 
                          INNER JOIN residents AS u ON r.user_id = u.id
                          WHERE r.finish = 'Ongoing'";

                  // PostgreSQL query execution
                  $result = pg_query($conn, $sql);
                  
                  // Fetch rows from the result
                  while ($row = pg_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>".$row['user']."</td>";
                    echo "<td>".$row['name']."</td>";
                    echo "<td>".$row['category']."</td>";
                    echo "<td>".$row['description']."</td>";
                    echo "<td>".$row['witness']."</td>";
                    echo "<td>".$row['file_date']."</td>";
                    echo "<td>".$row['police']."</td>";
                    echo "<td><center><a class='btn btn-sm' href='adminviewreports.php?id=".$row['id']."&id2=3' style='background-color: #184965;color: white;'>View</a></center></td>";
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

  <!-- Modal -->
  <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" id="modalheader">
          <h5 class="modal-title" id="idtitle">Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p style="font-size: 20px;" id="modalDate"></p>
          <p style="text-align: center;font-size: 20px;">Are you sure you want to <span id="modalAction"></span> Mr/Ms. <span id="modalName"></span> ? </p>
          <center><button id="btnbtn1" class="btn btn-primary" style="display: none;">Accept</button></center>
          <center><button id="btnbtn2" class="btn btn-danger" style="display: none;">Decline</button></center>
          <input type="hidden" value="" id="idd">
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
