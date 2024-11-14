<?php 
session_start();
include "dbcon.php";

// Check for session role
if (!isset($_SESSION['role']) || (trim ($_SESSION['role']) == '')) {
    header('location:main.php');
    exit();
}

if (isset($_GET['category'])) {
    $_SESSION['category'] = $_GET['category'];
}

if (isset($_GET['status'])) {
    $_SESSION['status'] = $_GET['status'];  // Set the session status based on folder click
}

$status = isset($_GET['status']) ? pg_escape_string($conn, $_GET['status']) : '';
$category = isset($_GET['category']) ? pg_escape_string($conn, $_GET['category']) : (isset($_SESSION['category']) ? $_SESSION['category'] : '');
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
  <?php include "nav3.php"; ?>
  
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Files</h1>
    </div>

    <section class="section dashboard">
      <div class="col-12">
        <div class="card recent-sales overflow-auto">
          <div class="card-body">
            <div class="error-message">
              <?php
              if (!empty($_GET['error_message'])) {
                $co = isset($_GET['color']) ? $_GET['color'] : 'p';
                echo $co == "p" 
                  ? "<div class='alert alert-primary' style='text-align:center;' role='alert'> " . $_GET['error_message'] . "</div>"
                  : "<div class='alert alert-danger' style='text-align:center;' role='alert'> " . $_GET['error_message'] . "</div>";
              }
              ?>
            </div>
          </div>
          <div class="card-body mt-3">
            <form action="documents1.php" method="post">
              <div class="input-group mb-5">
                <input type="text" class="form-control" placeholder="User name" required name="username">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
                <a class="btn btn-outline-secondary" href="documents.php">Reset</a>
              </div>
            </form>

            <div class="container text-center">
              <div class="row row-cols-2 row-cols-sm-2 row-cols-md-6">
                <?php
                $id = $_SESSION['id'];

                // Query to retrieve residents with selected category and status
                $sql_residents = "SELECT DISTINCT u.fullname, a.user_id 
                                  FROM reports a
                                  INNER JOIN residents u ON a.user_id = u.id 
                                  WHERE a.category = '$category' 
                                  AND a.finish = '$status' 
                                  AND a.police_assign = '$id'";

                // Execute the PostgreSQL query
                $result_residents = pg_query($conn, $sql_residents);

                if (pg_num_rows($result_residents) > 0) {
                  while ($row = pg_fetch_assoc($result_residents)) {
                    echo "<div class='col mb-3'>";
                    echo "<a href='showfiles.php?id=" . $row['user_id'] . "&status=" . urlencode($_SESSION['status']) . "&category=" . urlencode($_SESSION['category']) .  "' class='d-block text-decoration-none'>";
                    echo "<img src='folder.png' width='50px' class='mb-2'>";
                    echo "<div>" . htmlspecialchars($row['fullname']) . "</div>";
                    echo "</a>";
                    echo "</div>";
                  }
                } else {
                  echo "<div class='col-12'>No residents found for this category and status.</div>";
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

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
