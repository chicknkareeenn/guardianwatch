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
      <h1>New Report
      <div class="float-end">
        <button class="btn btn-sm" style="background-color: #184965; color: white;" data-bs-toggle="modal" data-bs-target="#exampleModal">
            <i class="bi bi-plus-lg"></i> Add New Report
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
                                // PostgreSQL query syntax
                                $sql = "
                                    SELECT r.id, u.fullname AS username, r.name, 
                                           r.category, r.description, r.file_date, 
                                           r.finish, r.witness, r.status
                                    FROM reports AS r
                                    INNER JOIN residents AS u ON r.user_id = u.id
                                    WHERE r.status = 'Acceptable' AND r.finish IS NULL or r.finish = '';
                                ";

                                // Use PostgreSQL connection
                                $result = pg_query($conn, $sql); // `pg_query` for PostgreSQL
                                while ($row = pg_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['username'] . "</td>";
                                    echo "<td>" . $row['name'] . "</td>";
                                    echo "<td>" . $row['category'] . "</td>";
                                    echo "<td>" . $row['description'] . "</td>";
                                    echo "<td>" . $row['file_date'] . "</td>";
                                    echo "<td>
                                        <button class='btn btn-sm btn-primary' onclick='callmodal1(\"" . $row['id'] . "\", \"" . $row['category'] . "\")'>Assign</button>
                                        <button class='btn btn-danger btn-sm' onclick='callmodal(\"" . $row['id'] . "\")'>Reject</button>
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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>

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
                                        <input type='hidden' id="arrid1" name='id'>
                                        <input type='hidden' id="category" name='category'> <!-- Hidden field for category -->

                                        <select class="form-select" aria-label="Default select example" name="police" id="police-select">
                                            <option selected>Open this select menu</option>
                                            <!-- Options will be filled by JavaScript -->
                                        </select>
                                        <div class='modal-footer'>
                                          <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                          <button type='submit' class='btn btn-primary'>Assign</button>
                                      </div>
                                    </form>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #184965;color: white;">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Report</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Step indicators -->
                <div class="d-flex justify-content-center mb-3">
                    <ul class="nav nav-pills mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" id="step1-tab" href="#"><i class="bi bi-1-square"></i></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="step2-tab" href="#"><i class="bi bi-2-square"></i></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="step3-tab" href="#"><i class="bi bi-3-square"></i></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="step4-tab" href="#"><i class="bi bi-4-square"></i></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="step5-tab" href="#"><i class="bi bi-5-square"></i></a>
                        </li>
                    </ul>
                </div>

                <form id="multiStepForm" action="addreports.php" method="post" enctype='multipart/form-data'>
                    <!-- Step 1 -->
                    <div class="step" id="step1">

                      <center><h4><b>Select Crime Category</b></h4></center>
                        <select class="form-select" aria-label="Default select example" name="crime">
                          <option selected>Select Crime Category</option>
                          <?php
                        $sql = "SELECT * FROM categories";
                        $result = pg_query($conn, $sql);
                        
                        if ($result) {
                            while ($row = pg_fetch_array($result)) {
                                echo "<option value='".$row['category']."'>".$row['category']."</option>";
                            }
                        } 
                        ?>
                        </select>
                        <div class="form-floating mt-3 mb-3">

                          
                        </div>
                        <button type="button" class="btn" style="background-color:#184965;color: white;" id="next1">Next</button>
                    </div>
                    <!-- Step 2 -->
                    <div class="step" id="step2" style="display:none;">
                        <center><h4><b>Personal information</b></h4></center>
                        
                        <div class="mb-3">
                            <label for="step2Input1" class="form-label">Name of the Victim</label>
                            <input type="text" class="form-control" id="step2Input1" name="victim" required>
                        </div>

                        <div class="mb-3">
                            <select class="form-select" aria-label="Default select example" name="address" required>
                                <option selected>Select Barangay</option>
                                <?php
                                $sql = "SELECT * FROM barangay";
                                $result = pg_query($conn, $sql);
                                if ($result) {
                                    while ($row = pg_fetch_array($result)) {
                                        echo "<option value='".$row['id']."'>".$row['barangay']."</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="step2Input2" class="form-label">Contact of the Victim</label>
                            <input type="text" class="form-control" id="step2Input2" name="contact" required>
                        </div>

                        <!-- Checkbox to indicate if there is a witness -->
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="witnessCheckbox">
                            <label class="form-check-label" for="witnessCheckbox">Does the Victim have a witness?</label>
                        </div>

                        <!-- Witness fields, initially hidden -->
                        <div id="witnessFields" style="display:none;">
                            <div class="mb-3">
                                <label for="step2Input2" class="form-label">Name of the Witness</label>
                                <input type="text" class="form-control witnessName" name="witness[]" placeholder="Witness Name">
                            </div>
                            <div class="mb-3">
                                <label for="step2Input2" class="form-label">Contact</label>
                                <input type="text" class="form-control witnessContact" name="witcontact[]" placeholder="Witness Contact">
                            </div>

                            <!-- Button to add another witness -->
                            <button type="button" class="btn btn-info" id="addWitness">Add Another Witness</button>
                        </div>

                        <button type="button" class="btn btn-secondary" id="prev2">Previous</button>
                        <button type="button" class="btn" style="background-color:#184965;color: white;" id="next2">Next</button>
                    </div>
                    <!-- Step 3 -->
                    <div class="step" id="step3" style="display:none;">
                               <center><h4><b>Crime information</b></h4></center>
                        <div class="mb-3">
                            <label for="step3Input1" class="form-label">Date of Crime</label>
                            <input type="date" class="form-control" id="step3Input1" name="datecrime" required>
                        </div>
                        <div class="mb-3">
                            <label for="step3Input2" class="form-label">Time of Crime</label>
                            <input type="time" class="form-control" id="step3Input2" name="timecrime" required>
                        </div>
                        <div class="mb-3">
                        <label for="floatingTextarea2">Describe the Scenario</label>
                        <textarea class="form-control" placeholder="Ilahad ang Sanaysay" id="floatingTextarea2" name="description" style="height: 100px"></textarea>
                        </div>
                        <button type="button" class="btn btn-secondary" id="prev3">Previous</button>
                        <button type="button" class="btn " style="background-color:#184965;color: white;" id="next3">Next</button>
                    </div>
                    <!-- Step 4 -->
                    <div class="step" id="step4" style="display:none;">
                        <center><h4><b>Account Creation</b></h4></center>
                        <div class="mb-3">
                            <label for="step4Input3" class="form-label">Birth Date</label>
                            <input type="date" class="form-control" id="step3Input1" name="birthdate" required>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Gender</label>
                          <div style="display: flex; align-items: center;">
                              <div style="margin-right: 10px;">
                                  <input type="radio" id="male" name="gender" value="Male" required>
                                  <label for="male">Male</label>
                              </div>
                              <div>
                                  <input type="radio" id="female" name="gender" value="Female" required>
                                  <label for="female">Female</label>
                              </div>
                          </div>
                      </div>
                      <div class="mb-3">
                                <label for="step4Input4" class="form-label">Email Address</label>
                                <input type="text" class="form-control witnessName" name="email" required>
                            </div>
                        <div class="mb-3">
                            <label for="step4Input1" class="form-label">Default Username</label>
                            <input type="text" class="form-control" id="step4Input1" name="username" required readonly>
                        </div>
                        <div class="mb-3">
                            <label for="step4Input2" class="form-label">Default Password</label>
                            <input type="text" class="form-control" id="step4Input2" name="password" required readonly>
                        </div>
                        <button type="button" class="btn btn-secondary" id="prev4">Previous</button>
                        <button type="button" class="btn" style="background-color:#184965;color: white;" id="next4">Next</button>
                    </div>
                    <!-- Step 5 -->
                    <div class="step" id="step5" style="display:none;">
                      <center><h4><b>Submit Report</b></h4></center>


                       <div class="form-check">
  <input class="form-check-input" type="checkbox"  id="step5Input1">
  <label class="form-check-label" for="flexCheckDefault">
   Agreement of Truthfulness and Access Rights
  </label>
</div>
<p class="mt-3 mb-3" style="text-align: justify;">
I hereby acknowledge and affirm that all information and evidence provided are true and accurate to the best of my knowledge. I understand and agree that this information may be accessed by authorized personnel, including administrators and law enforcement officers assigned to this matter, for the purposes of investigation and verification. By signing this agreement, I consent to the disclosure and examination of the information by these parties as required for the lawful and proper handling of the case.</p>


                        <button type="button" class="btn btn-secondary" id="prev5">Previous</button>
                        <button type="submit" class="btn" style="background-color:#184965;color: white;" id="submitBtn" disabled>Submit</button>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn" style="background-color: #57737A;color: white;" data-bs-dismiss="modal">Close</button>
               
            </div>
        </div>
    </div>
</div>

<script>
// Show/hide witness fields based on checkbox
document.getElementById('witnessCheckbox').addEventListener('change', function () {
    var witnessFields = document.getElementById('witnessFields');
    if (this.checked) {
        witnessFields.style.display = 'block'; // Show witness fields
    } else {
        witnessFields.style.display = 'none'; // Hide witness fields
    }
});

// Add another witness field when the button is clicked
document.getElementById('addWitness').addEventListener('click', function () {
    var witnessFields = document.getElementById('witnessFields');
    
    var witnessName = document.createElement('div');
    witnessName.classList.add('mb-3');
    witnessName.innerHTML = '<label for="step2Input2" class="form-label">Name of the Witness</label><input type="text" class="form-control witnessName" name="witness[]" placeholder="Witness Name">';
    
    var witnessContact = document.createElement('div');
    witnessContact.classList.add('mb-3');
    witnessContact.innerHTML = '<label for="step2Input2" class="form-label">Contact</label><input type="text" class="form-control witnessContact" name="witcontact[]" placeholder="Witness Contact">';
    
    witnessFields.appendChild(witnessName);
    witnessFields.appendChild(witnessContact);
});
</script>
                   
<script type="text/javascript">
function callmodal1(reportId, category) {
    document.getElementById("arrid1").value = reportId;
    document.getElementById("category").value = category; // Set the category in the hidden field

    // Fetch police based on the category
    fetchPoliceByCategory(category);

    var myModal = new bootstrap.Modal(document.getElementById('asign'));
    myModal.show();
}
function callmodal(id){
document.getElementById("arrid").value = id;
    console.log(id);
    var myModal = new bootstrap.Modal(document.getElementById('arr'));
    myModal.show();
  }

function fetchPoliceByCategory(category) {
    const policeSelect = document.getElementById("police-select");
    policeSelect.innerHTML = ""; // Clear previous options

    // Fetch police officers based on the category
    fetch(`fetchPolice.php?category=${category}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                data.forEach(police => {
                    const option = document.createElement("option");
                    option.value = police.id;
                    option.textContent = police.fullname;
                    policeSelect.appendChild(option);
                });
            } else {
                const option = document.createElement("option");
                option.textContent = "No police available for this category";
                policeSelect.appendChild(option);
            }
        })
        .catch(error => console.error('Error fetching police:', error));
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

  <script>

const x = document.getElementById("step4Input");

 function getLocation() {
   if (navigator.geolocation) {
     navigator.geolocation.getCurrentPosition(showPosition);
   } else { 
     x.value = "Geolocation is not supported by this browser.";
   }
 }

 function showPosition(position) {
   x.value = position.coords.latitude +","+ position.coords.longitude;

   console.log(position.coords.latitude);
 }
setInterval(getLocation, 5000);

document.addEventListener('DOMContentLoaded', function() {
       const checkbox = document.getElementById('step5Input1');
       const submitBtn = document.getElementById('submitBtn');

       checkbox.addEventListener('change', function() {
           submitBtn.disabled = !checkbox.checked;
       });
   });

$(document).ready(function() {
// Function to check if all inputs, selects, and textareas in the current step are filled
function validateStep(currentStep) {
    let isValid = true;

    // Iterate through the fields and check if they are required and empty
    $(currentStep + ' input, ' + currentStep + ' select, ' + currentStep + ' textarea').each(function() {
        // Check if the field is required and if it is empty
        if ($(this).prop('required') && $(this).val() === '') {
            isValid = false;
        }
    });

    return isValid;
}

// Function to show the specified step
function showStep(step) {
   $('.step').hide();
   $(step).show();
}

// Function to validate and navigate
function validateAndNavigate(currentStep, nextStep, currentTab, nextTab) {
   if (validateStep(currentStep)) {
       showStep(nextStep);
       $(currentTab).removeClass('active');
       $(nextTab).addClass('active');
       $(currentTab).addClass('completed');  // Mark the current step as completed
   } else {
       alert('Please fill out all required fields.');
   }
}

// Add click event listeners to the step tabs
$('.nav-link').click(function() {
   let stepId = $(this).attr('id').replace('-tab', '');
   let currentStep = $('.step:visible').attr('id');
   if (validateStep('#' + currentStep)) {
       showStep('#' + stepId);
       $('.nav-link').removeClass('active');
       $(this).addClass('active');
       $('#' + currentStep + '-tab').addClass('completed');  // Mark the previous step as completed
   } else {
       alert('Please fill out all required fields.');
   }
});

// Next button for step 1
$('#next1').click(function() {
   validateAndNavigate('#step1', '#step2', '#step1-tab', '#step2-tab');
});

// Next button for step 2
$('#next2').click(function() {
   validateAndNavigate('#step2', '#step3', '#step2-tab', '#step3-tab');
});

// Next button for step 3
$('#next3').click(function() {
   validateAndNavigate('#step3', '#step4', '#step3-tab', '#step4-tab');
});

// Next button for step 4
$('#next4').click(function() {
   validateAndNavigate('#step4', '#step5', '#step4-tab', '#step5-tab');
});

// Previous buttons
$('#prev2').click(function() {
   showStep('#step1');
   $('#step2-tab').removeClass('active');
   $('#step1-tab').addClass('active');
});

$('#prev3').click(function() {
   showStep('#step2');
   $('#step3-tab').removeClass('active');
   $('#step2-tab').addClass('active');
});

$('#prev4').click(function() {
   showStep('#step3');
   $('#step4-tab').removeClass('active');
   $('#step3-tab').addClass('active');
});

$('#prev5').click(function() {
   showStep('#step4');
   $('#step5-tab').removeClass('active');
   $('#step4-tab').addClass('active');
});


});
</script>
<script>
    function generateUsername() {
        // Generate random numbers (3 digits)
        const randomNumber = Math.floor(100 + Math.random() * 900); // Random number between 100-999
        return `GW-USER-${randomNumber}`;
    }

    document.addEventListener("DOMContentLoaded", function () {
        const usernameField = document.getElementById("step4Input1");
        const passwordField = document.getElementById("step4Input2");

        // Generate username
        const username = generateUsername();

        // Set username and password to be the same
        usernameField.value = username;
        passwordField.value = username;
    });
</script>

  </html>

