<?php 
 session_start();
 include "dbcon.php";
 if (!isset($_SESSION['role']) || (trim($_SESSION['role']) == '')) {
     header('location:main.php');
     exit();
 }  

 $policeAssign = isset($_SESSION['id']) ? $_SESSION['id'] : '';
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
  <link href="https://fonts.googleapis.com/css?family=Open+Sans|Nunito|Poppins" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <style type="text/css">
    .error-message {
      margin: 10px 0;
    }
    .modal1 {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.5);
    }
    .modal-content1 {
      background-color: #fff;
      margin: 5% auto;
      padding: 20px;
      border: 1px solid #ddd;
      width: 80%;
      max-width: 600px;
      text-align: center;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .close1 {
      color: #aaa;
      float: right;
      font-size: 24px;
      font-weight: bold;
      cursor: pointer;
    }
    .close1:hover,
    .close1:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }
    .modal-content1 h2 i {
      margin-right: 10px;
      color: #f0ad4e;
    }
    .file-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px;
      border-bottom: 1px solid #ddd;
    }
    .remove-btn {
      font-size: 0.75rem;
      padding: 0.25rem 0.5rem;
      color: #fff;
      background-color: #dc3545;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .remove-btn:hover {
      background-color: #c82333;
    }
    .announcement-form textarea {
      resize: vertical;
    }
    .announcement-form .form-control {
      border-radius: 4px;
    }
  </style>
</head>

<body>
  <?php include "nav3.php"; ?>
  
  <main id="main" class="main">
    <audio id="alertSound" src="alert.mp3" preload="auto"></audio>

    <div class="pagetitle">
      <h1>Announcements</h1>
    </div>

    <section class="section dashboard">
      <div class="col-12">
        <div class="card recent-sales overflow-auto">
          <div class="card-body">
            <div class="error-message">
              <?php
                if (!empty($_GET['error_message'])) {
                  $co = isset($_GET['color']) ? $_GET['color'] : 'p';
                  $alertClass = ($co == "p") ? "alert-primary" : "alert-danger";
                  echo "<div class='alert {$alertClass} text-center' role='alert'>{$_GET['error_message']}</div>";
                }
              ?>
            </div>
          </div>
          
          <div class="card-body mt-3">
            <form action="post_announcement.php" method="post" enctype="multipart/form-data" class="announcement-form">
              <div class="mb-3">
                <label for="announcement" class="form-label">Announcement</label>
                <textarea class="form-control" id="announcement" name="announcement" rows="4" required></textarea>
              </div>
              <div class="mb-3">
                <label for="announcement_files" class="form-label">Upload Files</label>
                <input type="file" class="form-control" id="announcement_files" name="announcement_files[]" multiple>
              </div>
              <button type="submit" class="btn btn-primary">Post Announcement</button>
            </form>
          </div>

          <div class="card-body mt-3">
            <h5 class="card-title">Recent Announcements</h5>
            <ul class="list-group">
              <?php
                $sql = "SELECT * FROM announcements ORDER BY created_at DESC";
                $result = mysqli_query($conn, $sql);
                
                while ($row = mysqli_fetch_array($result)) {
                    $announcement = $row['announcement'];
                    $files = json_decode($row['files'], true); // Decode the JSON-encoded file paths
                    
                    // Normalize file paths
                    $files = array_map(function($file) {
                        return str_replace('\\', '/', $file);
                    }, $files);
                
                    echo "<li class='list-group-item'>
                            <p>{$announcement}</p>";
                
                    if (!empty($files)) {
                        echo "<div class='file-list'>
                                <p>Attached Files:</p>
                                <ul>";
                        foreach ($files as $file) {
                            $fileName = basename($file);
                            echo "<li><a href='$file' download>$fileName</a></li>";
                        }
                        echo "</ul></div>";
                    }
                    
                    echo "<small class='text-muted'>Posted on {$row['created_at']}</small>
                          </li>";
                }
                ?>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <div id="emergencyAlertModal" class="modal1">
      <div class="modal-content1">
        <span class="close1">&times;</span>
        <h2 style="color:red; font-weight:bolder;"><i class="fa fa-exclamation-triangle"></i> Emergency Alert!</h2>
        <p id="emergencyLocation"></p>
      </div>
    </div>
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
  <script src="assets/js/main.js"></script>

  <div class='modal fade' id="asign" tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
    <div class='modal-dialog'>
      <div class='modal-content'>
        <div class='modal-header bg-primary text-white'>
          <h5 class='modal-title' id='exampleModalLabel'>Status Update</h5>
          <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
        </div>
        <div class='modal-body'>
          <form method='POST' action='finish.php' id='finish-form'>
            <input type='hidden' id='report_id' name='report_id'>
            <input type='hidden' id='user_id' name='user_id'>
            <div class='mb-3'>
              <label for='status' class='form-label'>Select Status</label>
              <select class='form-select' id='status' name='status' required>
                <option value='Finished'>Finished</option>
              </select>
            </div>
            <button type='submit' class='btn btn-primary'>Save changes</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', (event) => {
      const alertSound = document.getElementById('alertSound');
      const emergencyAlertModal = document.getElementById('emergencyAlertModal');
      const close1 = document.querySelector('.close1');
      
      const ws = new WebSocket('ws://localhost:8080');
      ws.onmessage = (event) => {
        const message = JSON.parse(event.data);
        if (message.type === 'emergencyAlert') {
          document.getElementById('emergencyLocation').innerText = message.location;
          emergencyAlertModal.style.display = 'block';
          alertSound.play();
        }
      };

      close1.addEventListener('click', () => {
        emergencyAlertModal.style.display = 'none';
      });

      window.addEventListener('click', (event) => {
        if (event.target == emergencyAlertModal) {
          emergencyAlertModal.style.display = 'none';
        }
      });

      document.getElementById('asign').addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        const reportId = button.getAttribute('data-id');
        const userId = button.getAttribute('data-user');

        const modal = document.getElementById('asign');
        modal.querySelector('#report_id').value = reportId;
        modal.querySelector('#user_id').value = userId;
      });
    });
  </script>
</body>

</html>
