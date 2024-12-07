<?php 
 session_start();
   include "dbcon.php";
  if (!isset($_SESSION['role']) ||(trim ($_SESSION['role']) == '')) {
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-<hash>" crossorigin="anonymous" />

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Updated: Nov 17 2023 with Bootstrap v5.3.2
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
  <style type="text/css">
    .error-message {
            height: 10px; /* Fixed height for the error message container */
            margin-bottom: 10px;
            margin-top: 10px;

        }
    .modal1 {
      display: none; /* Hidden by default */
      position: fixed; /* Stay in place */
      z-index: 1000; /* Sit on top */
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto; /* Enable scrolling if needed */
      background-color: rgba(0,0,0,0.5); /* Black w/ opacity */
    }
    .modal-content1 {
      background-color: #fefefe;
      margin: 15% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
      max-width: 600px;
      text-align: center;
      border-radius: 8px;
    }
    .close1 {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }
    .close1:hover,
    .close1:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }
    .modal-content h2 i {
      margin-right: 18px;
      color: #f0ad4e; /* Icon color (yellow) */
    }
    #fileList {
        display: flex;
        flex-direction: column;
    }

    .file-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px 0;
    }

    .remove-btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        margin-left: 1rem;
    }

    .remove-btn:hover {
        background-color: #c82333;
        color: white;
    }
  </style>
</head>

<body>
  <?php
    include "nav3.php";
  ?>
  
  

  <main id="main" class="main">
  <audio id="alertSound" src="alert.mp3" preload="auto"></audio>

    <div class="pagetitle">
      <h1>New Report</h1>

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

                  <!-- <h5 class="card-title">Landlord List</h5> -->
                  

                  <table class="table table-borderless datatable mt-2" id="data-table">
                    <thead>
                      <tr>
                        <th scope="col">User Reported</th>
                        <th scope="col">Victim Name</th>
                        <th scope="col">Case</th>
                        <th scope="col">Details</th>
                        <th scope="col">File Date</th>
                        <th scope="col">Action</th>

                      
                      </tr>
                    </thead>
                    <tbody>
                      <?php

                       
            $sql = "
            SELECT 
                r.id, 
                u.fullname AS username, 
                r.name,
                r.category,
                r.description,
                TO_CHAR(r.file_date, 'Mon-DD-YYYY') AS file_date, 
                r.finish,
                r.police_assign,
                r.user_id
            FROM 
                reports AS r
            INNER JOIN 
                residents AS u 
            ON 
                r.user_id = u.id
            WHERE
                r.police_assign = '$policeAssign' AND 
                r.finish = 'Unsettled';
            ";
                    $result = pg_query($conn, $sql);
                    while ($row = pg_fetch_array($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['username'] . "</td>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['category'] . "</td>";
                        echo "<td>" . $row['description'] . "</td>";
                        echo "<td>" . $row['file_date'] . "</td>";
                        echo "<td>
                            <button class='btn btn-sm btn-primary' onclick='callmodal1(\"" . $row['id'] . "\", \"" . $row['user_id'] . "\", \"" . $row['name'] . "\", \"" . $row['category'] . "\", \"" . $row['description'] . "\", \"" . $row['file_date'] . "\")'>Update</button>
                        </td>";
                        echo "</tr>";

                      }
                      ?>
                    
                      
                    </tbody>
                  </table>

                </div>

              </div>
            </div><!-- End Recent Sales -->

    </section>
    <div id="emergencyAlertModal" class="modal1">
      <div class="modal-content1">
        <span class="close1">&times;</span>
        <h2 style="color:red; font-weight:bolder;"><i class="fa fa-exclamation-triangle"></i>Emergency Alert!</h2>
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

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

<div class='modal fade' id="asign" tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header bg-primary text-white'>
                <h1 class='modal-title fs-5' id='exampleModalLabel'>Upload Files and Notes</h1>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                <form action='uploadFiles.php' method='post' enctype='multipart/form-data'>
                    <input type='hidden' id="arrid1" name='id'>
                    <input type='hidden' id="userId" name='user_id'>
                    <input type='hidden' id="name" name='name'>
                    <input type='hidden' id="category" name='category'>
                    <input type='hidden' id="description" name='description'>
                    <input type='hidden' id="file_date" name='file_date'>
                    <input type='hidden' id="policeId" name='policeId' value="<?php echo $policeAssign; ?>">

                    <div class="mb-3">
                        <label for="files" class="form-label">Upload Files</label>
                        <input type="file" class="form-control" id="files" name="files" onchange="handleFileSelect(event)">
                    </div>

                    <div id="fileList" class="mb-3"></div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                        <button type='submit' class='btn btn-primary'>Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let fileArray = [];

    function handleFileSelect(event) {
    const fileList = document.getElementById('fileList');
    const file = event.target.files[0]; // Get the first selected file

    // Clear the file array and display the selected file
    fileList.innerHTML = ''; // Clear any previous file list
    if (file) {
        const fileDiv = document.createElement('div');
        fileDiv.className = 'file-item';

        const fileName = document.createElement('span');
        fileName.textContent = file.name;

        const removeButton = document.createElement('button');
        removeButton.textContent = 'Remove';
        removeButton.type = 'button';
        removeButton.className = 'btn btn-danger btn-sm ms-2';
        removeButton.onclick = function () {
            removeFile();
        };

        fileDiv.appendChild(fileName);
        fileDiv.appendChild(removeButton);

        fileList.appendChild(fileDiv);
    }
}

function removeFile() {
    // Reset the file input to allow file removal
    document.getElementById('files').value = '';
    document.getElementById('fileList').innerHTML = '';
}
</script>

<script type="text/javascript">
  
  
  function callmodal1(id, user_id, name, category, description, file_date) {
    document.getElementById("arrid1").value = id;
    document.getElementById('userId').value = user_id;
    document.getElementById('name').value = name;
    document.getElementById('category').value = category;
    document.getElementById('description').value = description;
    document.getElementById('file_date').value = file_date;
    console.log("Selected Report ID:", id); // Debugging
    var myModal = new bootstrap.Modal(document.getElementById('asign'));
    myModal.show();
}

</script>
<script>
  document.addEventListener('DOMContentLoaded', (event) => {
    if (Notification.permission !== "granted") {
      Notification.requestPermission();
    }
  });

  function showEmergencyAlert(location) {
    // Show the modal
    const modal = document.getElementById('emergencyAlertModal');
    const modalLocation = document.getElementById('emergencyLocation');
    modalLocation.textContent = `Emergency Alert: ${location}`;
    modal.style.display = 'block';

    // Function to play alert sound with volume set to 70%
    function playAlertSound() {
      alertSound.volume = 0.1; // Set volume to 70% (0.7 is 70% of max volume)
      alertSound.play();
    }

    // Play alert sound when showing the modal
    playAlertSound();

    // Text-to-speech function
    function speakText(text) {
      if ('speechSynthesis' in window) {
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.rate = 1; // Set speech rate
        utterance.pitch = 1; // Set pitch
        utterance.volume = 5; // Set volume
        window.speechSynthesis.speak(utterance);
      } else {
        console.error('Speech Synthesis not supported in this browser.');
      }
    }

    // Speak the alert message
    speakText(`Emergency Alert: ${location}`);

    // Close the modal when the user clicks on the close button (×)
    const closeButton = document.getElementsByClassName('close1')[0];
    closeButton.onclick = function() {
      modal.style.display = 'none';
    };

    // Close the modal if the user clicks anywhere outside of the modal content
    window.onclick = function(event) {
      if (event.target === modal) {
        modal.style.display = 'none';
      }
    };

    // Show browser notification
    if (Notification.permission === "granted") {
      const notification = new Notification('Emergency Alert!', {
        body: `Emergency Alert: ${location}`,
        icon: './img/logo.png' // Path to an icon image
      });

      notification.onclick = function() {
        window.focus();
        modal.style.display = 'block'; // Ensure modal is visible if clicked
      };
    }
  }

  // WebSocket setup
  const ws = new WebSocket('ws://localhost:8080');
  const alertSound = document.getElementById('alertSound');

  ws.onopen = () => {
    console.log('WebSocket connection established');
  };

  ws.onmessage = (message) => {
    const data = JSON.parse(message.data);

    if (data.type === 'emergencyAlert') {
      showEmergencyAlert(data.data.combinedLocation);
    }
  };

  ws.onclose = () => {
    console.log('WebSocket connection closed');
  };

  ws.onerror = (error) => {
    console.error('WebSocket error: ', error);
  };
  </script>
  </html>
