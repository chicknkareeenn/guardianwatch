<?php 
session_start();
include "dbcon.php"; // Ensure this contains the PostgreSQL connection

if (!isset($_SESSION['role']) ||(trim ($_SESSION['role']) == '')) {
    header('location:main.php');
    exit();
}  

$category = isset($_GET['category']) ? pg_escape_string($conn, $_GET['category']) : (isset($_SESSION['category']) ? $_SESSION['category'] : '');
$status = isset($_GET['status']) ? pg_escape_string($conn, $_GET['status']) : (isset($_SESSION['status']) ? $_SESSION['status'] : '');
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
    include "nav3.php";
  ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1><a href="documents.php">Files</a> > <?php  
      $id = $_GET['id'];
      $sql = "SELECT fullname FROM residents WHERE id = $1"; // Use parameterized queries
      $result = pg_query_params($conn, $sql, array($id));
      if ($row = pg_fetch_array($result)) {
        echo $row['fullname'];
      }
      ?></h1>

      <nav></nav>
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
            <form action="showfiles1.php?id=<?php echo $_GET['id']?>" method="post">
              <div class="input-group mb-5">
                <input type="text" class="form-control" placeholder="File name" name="file" required aria-label="Recipient's username" aria-describedby="button-addon2">
                <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Search</button>
            </form>
            <a class="btn btn-outline-secondary" type="button" id="button-addon2" href="showfiles.php?id=<?php echo $_GET['id']?>">Reset</a>
            </div>
              
            <div class="container text-center">
              <div class="row row-cols-2 row-cols-sm-2 row-cols-md-6">
                <?php
                $id = $_SESSION['id'];
                $id2 = $_GET['id'];
                $sql = "SELECT a.valid_id, a.file_date, a.police_assign, a.id, u.fullname, a.user_id
                        FROM reports AS a
                        INNER JOIN residents AS u ON a.user_id = u.id
                        WHERE a.police_assign = $1 AND a.user_id = $2 AND a.category = $3 AND a.finish = $4
                        ORDER BY fullname"; // Use parameterized queries

                $result = pg_query_params($conn, $sql, array($id, $id2, $category, $status));

                while ($row = pg_fetch_array($result)) {
                    $extension = pathinfo($row['valid_id'], PATHINFO_EXTENSION);

                    // Determine the icon based on the file extension
                    if ($extension == "pdf") {
                        $icon = 'pdf.png';
                    } elseif ($extension == "jpeg" || $extension == "jpg") {
                        $icon = 'jpeg.jpg';
                    } else {
                        $icon = 'docs.png';
                    }

                    // Output the image and name
                    echo "<div class='col text-center'>
                            <a href='uploads/".$row['valid_id']."' download='".$row['valid_id']."'><img src='$icon' width='50px' class='mb-2'></a>
                            <div>" . $row['valid_id'] . "</div>
                            <div>" . $row['id'] . "</div>
                            <div>" . $row['file_date'] . "</div>
                          </div>";
                }
                ?>
              </div>
            </div>
          </div>
        </div><!-- End Recent Sales -->
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
