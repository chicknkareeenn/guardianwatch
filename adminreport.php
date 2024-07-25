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
  </style>
</head>

<body>
  <?php
    include "nav1.php";
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
                        <th scope="col">Witness Name</th>
                        <th scope="col">Action</th>

                      
                      </tr>
                    </thead>
                    <tbody>
                      <?php

                       
                      $sql = "SELECT r.id, u.fullname AS username, r.name,
    r.category,
    r.description,
    r.file_date,
    r.finish,
    r.witness
FROM 
    reports AS r
INNER JOIN 
    residents AS u 
ON 
    r.user_id = u.id
WHERE
    r.finish !='Reject' and r.finish !='Requirements' and r.finish !='Ongoing' and r.finish != 'Unsettled'";
                      $result = mysqli_query($conn, $sql);
                      while ($row = mysqli_fetch_array($result)) {
                        echo "<tr>";
                        echo "<td>".$row['username']."</td>";
                        echo "<td>".$row['name']."</td>";
                        echo "<td>".$row['category']."</td>";
                        echo "<td>".$row['description']."</td>";
                        echo "<td>".$row['file_date']."</td>";
                        echo "<td>".$row['witness']."</td>";

                        echo "<td>
                        <button class='btn btn-sm btn-primary' onclick='callmodal1(\"".$row['id']."\")'>Assign</button> 

                        ";
                         echo"<button class='btn btn-danger btn-sm' onclick='callmodal(\"".$row['id']."\")'>Reject</button>
                        </td>";




                   

                        
                     
                      

              
                      



                        
                       

                        // para sa edit
                       
                        // para sa delete
                       
                     
                      
                      


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
<div class='modal fade' id="arr" tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                        <div class='modal-dialog'>
                          <div class='modal-content'>
                            <div class='modal-header bg-danger text-white'>
                              <h1 class='modal-title fs-5' id='exampleModalLabel'>Reject</h1>
                              <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                              <form action='rejectreport.php' method='post'>
                              <input type='hidden' id="arrid" name='id' >
                              <input type='hidden' name='n' value='2'>
                            <div class="form-floating">
                              <textarea class="form-control" placeholder="Leave a comment here" name="reason" id="floatingTextarea2" style="height: 100px" required></textarea>
                              <label for="floatingTextarea2">Please Provide Reason</label>
                            </div>                             
                            </div>
                            <div class='modal-footer'>
                              <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                              <button type='submit' class='btn btn-danger' >Reject</button>
                              </form>

                            </div>
                          </div>
                        </div>
                      </div>

<div class='modal fade' id="asign" tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                        <div class='modal-dialog'>
                          <div class='modal-content'>
                            <div class='modal-header bg-primary text-white'>
                              <h1 class='modal-title fs-5' id='exampleModalLabel'>Assign Police</h1>
                              <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                              <form action='assignreport.php' method='post'>
                              <input type='hidden' id="arrid1" name='id' >

                                <select class="form-select" aria-label="Default select example" name="police">
                                  <option selected>Open this select menu</option>

                                 <?php
                                  $sql = 'SELECT * FROM police WHERE status != "Not Available"';
                                  $result = mysqli_query($conn, $sql);

                                  while ($row = mysqli_fetch_array($result)) {
                                      echo "<option value='" . $row['id'] . "'>" . $row['fullname'] . "</option>";
                                  }
                                  ?>
                                </select>
                                                                               
                            </div>
                            <div class='modal-footer'>
                              <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                              <button type='submit' class='btn btn-primary' >Assign</button>
                              </form>

                            </div>
                          </div>
                        </div>
                      </div>

<script type="text/javascript">
  
  function callmodal(id){
document.getElementById("arrid").value = id;
    console.log(id);
// document.getElementById("modaltitle").innerText = "Cage " +  id;
    var myModal = new bootstrap.Modal(document.getElementById('arr'));
    myModal.show();

  }
  function callmodal1(id){
document.getElementById("arrid1").value = id;
    console.log(id);
// document.getElementById("modaltitle").innerText = "Cage " +  id;
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

