
<?php 
 session_start();
   include "dbcon.php";
  if (!isset($_SESSION['role']) ||(trim ($_SESSION['role']) == '')) {
        header('location:main.php');
        exit();
    } 
  if (isset($_GET['category'])) {
      $_SESSION['category'] = $_GET['category'];  // Set the session status based on folder click
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
    include "nav3.php";
  ?>
  
  

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Files</h1>

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

                            echo "<div class='alert alert-primary' style='text-align:center;' role='alert'> " . $_GET['error_message'] . "</div>";
                          }
                          else{
                            echo "<div class='alert alert-danger' style='text-align:center;' role='alert'> " . $_GET['error_message'] . "</div>";

                          }
                        }
                        ?>
                    </div>
                </div>
                <div class="card-body mt-3">
                  <form action="documents1.php" method="post">
                  <div class="input-group mb-5">
                  <input type="text" class="form-control" placeholder="User name" required  name="username" aria-label="Recipient's username" aria-describedby="button-addon2">
                  <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Search</button>
                  <a class="btn btn-outline-secondary" type="button" id="button-addon2" href="documents.php">Reset</a>
                </div>
                </form>
                  
                  <div class="container text-center ">
                  <div class="row row-cols-2 row-cols-sm-2 row-cols-md-6">
                  <?php
                  $id = $_SESSION['id'];
                  // Retrieve distinct categories from the reports table
                  $sql_categories = "SELECT DISTINCT category FROM reports WHERE police_assign = $1";
                  // Prepare and execute the SQL query with PostgreSQL
                  $result_categories = pg_prepare($conn, "get_categories", $sql_categories);
                  $result_categories = pg_execute($conn, "get_categories", array($id));

                  // Check for query results and output the categories
                  while ($row_category = pg_fetch_assoc($result_categories)) {
                    $category = $row_category['category'];
                    echo "<div class='col'>";
                    echo "<a href='manage_documents.php?category=" . urlencode($category) . "'>";
                    echo "<img src='folder.png' width='50px' class='mb-2'>";
                    echo "<div>" . htmlspecialchars($category) . "</div>";
                    echo "</a>";
                    echo "</div>";
                  }
                ?>
                  </div>
                </div>
                  

                </div>

              </div>
            </div><!-- End Recent Sales -->

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


