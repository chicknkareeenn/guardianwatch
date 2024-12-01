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
      <h1>On Going</h1>
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
                  <th scope="col">File Date</th>
                  <th scope="col">Current Status</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
              <?php
                  // PostgreSQL Query
                  $sql = "SELECT r.id, r.name, r.police_assign, r.witness, u.fullname AS user, p.fullname AS police, r.category, r.description, r.file_date, r.finish 
                          FROM reports AS r 
                          INNER JOIN police AS p ON r.police_assign = p.id 
                          INNER JOIN residents AS u ON r.user_id = u.id
                          WHERE r.police_assign = $policeAssign and r.finish = 'Ongoing' or r.finish = 'Under Investigation' or r.finish = 'Investigation Done'"; // Using the $policeAssign variable safely
                  
                  $result = pg_query($conn, $sql);  // Execute query with pg_query() for PostgreSQL
                  if ($result) {
                      while ($row = pg_fetch_assoc($result)) {  // Use pg_fetch_assoc() to fetch rows
                        echo "<tr>";
                        echo "<td>".$row['user']."</td>";
                        echo "<td>".$row['name']."</td>";
                        echo "<td>".$row['category']."</td>";
                        echo "<td>".$row['description']."</td>";
                        echo "<td>".$row['file_date']."</td>";
                        echo "<td>".$row['finish']."</td>";
                        echo "<td><center>
                          <a class='btn btn-sm' href='policeviewreports.php?id=".$row['id']."&id2=3' style='background-color: #184965;color: white;margin-bottom: 5px;'>
                            <i class='bi bi-eye'></i>
                          </a>
                          <a class='btn btn-sm' href='#' style='background-color: #f39c12;color: white;' 
                            onclick='openUpdateModal(".$row['id'].", \"".$row['name']."\", \"".$row['category']."\", \"".$row['description']."\", \"".$row['file_date']."\", \"".$row['finish']."\")'>
                              <i class='bi bi-pencil'></i>
                          </a>
                        </center></td>";
                        echo "</tr>";
                      }
                  } else {
                      echo "<tr><td colspan='8'>No records found.</td></tr>";  // Handle no result case
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
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p><strong>User Reported:</strong> <span id="modalUser"></span></p>
          <p><strong>Victim Name:</strong> <span id="modalName"></span></p>
          <p><strong>Case:</strong> <span id="modalCategory"></span></p>
          <p><strong>Details:</strong> <span id="modalDescription"></span></p>
          <p><strong>Witness Name:</strong> <span id="modalWitness"></span></p>
          <p><strong>File Date:</strong> <span id="modalFileDate"></span></p>
          <p><strong>Police Assign:</strong> <span id="modalPolice"></span></p>
          <button id="caseClosedBtn" class="btn btn-danger mt-3">Close Case</button>
          <input type="hidden" id="reportId">
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateModalLabel">Case Updates</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Dropdown for Update Type -->
        <label for="updateType" class="form-label">Select Update Type:</label>
        <select id="updateType" class="form-select">
          <option value="" selected disabled>Select update type</option>
          <option value="status">Change Status of Case</option>
          <option value="schedule">Interview Schedule or Court Dates</option>
          <option value="closure">Case Closure Summary</option>
          <option value="followup">Follow-Up Requirements</option>
        </select>

        <!-- Dynamic Fields -->
        <div id="dynamicFields" class="mt-3">
          <!-- Fields will be inserted here dynamically -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" id="saveUpdateBtn" class="btn btn-primary">Save Update</button>
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

  <script>
    document.addEventListener('DOMContentLoaded', (event) => {
      // Event listener for view buttons
      document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          const user = this.getAttribute('data-user');
          const name = this.getAttribute('data-name');
          const category = this.getAttribute('data-category');
          const description = this.getAttribute('data-description');
          const witness = this.getAttribute('data-witness');
          const fileDate = this.getAttribute('data-file_date');
          const police = this.getAttribute('data-police');

          document.getElementById('modalUser').textContent = user;
          document.getElementById('modalName').textContent = name;
          document.getElementById('modalCategory').textContent = category;
          document.getElementById('modalDescription').textContent = description;
          document.getElementById('modalWitness').textContent = witness;
          document.getElementById('modalFileDate').textContent = fileDate;
          document.getElementById('modalPolice').textContent = police;
          document.getElementById('reportId').value = id;

          // Show modal
          var myModal = new bootstrap.Modal(document.getElementById('myModal'));
          myModal.show();
        });
      });

      // Event listener for Case Closed button
      document.getElementById('caseClosedBtn').addEventListener('click', function() {
        const reportId = document.getElementById('reportId').value;

        // AJAX request to update the finish column
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'close_case.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
          if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
              alert('Case closed successfully');
              location.reload();
            } else {
              alert('Failed to close the case');
            }
          }
        };
        xhr.send('id=' + reportId);
      });
    });
  </script>


<script>
  document.addEventListener('DOMContentLoaded', () => {
    const updateType = document.getElementById('updateType');
    const dynamicFields = document.getElementById('dynamicFields');
    const saveUpdateBtn = document.getElementById('saveUpdateBtn');
    const currentStatus = 'Ongoing'; // This would typically be fetched from the server or database

    // Listen for update type change
    updateType.addEventListener('change', () => {
      const type = updateType.value;
      dynamicFields.innerHTML = ''; // Clear any previous fields

      if (type === 'status') {
        dynamicFields.innerHTML = `
          <label for="caseStatus" class="form-label">Current Status:</label>
          <input type="text" id="caseStatus" class="form-control" value="${currentStatus}" readonly>
          
          <label for="newStatus" class="form-label mt-2">New Status:</label>
          <select id="newStatus" class="form-control">
            <option value="Under Investigation" ${currentStatus === 'Under Investigation' ? 'selected' : ''}>Under Investigation</option>
            <option value="Investigation Done" ${currentStatus === 'Investigation Done' ? 'selected' : ''}>Investigation Done</option>
          </select>
        `;
      } else if (type === 'schedule') {
            dynamicFields.innerHTML = `
              <label for="scheduleDetails" class="form-label">Interview or Court Date Details:</label>
              <textarea id="scheduleDetails" class="form-control" rows="3" placeholder="Enter schedule details"></textarea>
              
              <label for="scheduleDate" class="form-label mt-3">Select Date:</label>
              <input type="date" id="scheduleDate" class="form-control">
              
              <label for="scheduleTime" class="form-label mt-3">Select Time:</label>
              <input type="time" id="scheduleTime" class="form-control">
            `;
          }else if (type === 'closure') {
        dynamicFields.innerHTML = `
          <label for="closureSummary" class="form-label">Closure Summary:</label>
          <textarea id="closureSummary" class="form-control" rows="3" placeholder="Enter closure summary"></textarea>
          <label for="closureReason" class="form-label mt-2">Reasons for Resolution:</label>
          <textarea id="closureReason" class="form-control" rows="2" placeholder="Enter reasons"></textarea>
        `;
      } else if (type === 'followup') {
        dynamicFields.innerHTML = `
          <label for="followUpDetails" class="form-label">Follow-Up Requirements:</label>
          <textarea id="followUpDetails" class="form-control" rows="3" placeholder="Enter follow-up requirements"></textarea>
        `;
      } 
    });

    // Save Update Button Logic
    saveUpdateBtn.addEventListener('click', () => {
      const type = updateType.value;
      const reportId = document.getElementById('reportId').value; // Fetch the report ID

      // Collect data based on selected type
      let data = { reportId, type };
      if (type === 'status') {
        data.status = document.getElementById('newStatus').value;
      } else if (type === 'schedule') {
        data.scheduleDetails = document.getElementById('scheduleDetails').value;
        data.scheduleDate = document.getElementById('scheduleDate').value;
        data.scheduleTime = document.getElementById('scheduleTime').value;
    }  else if (type === 'closure') {
        data.closureSummary = document.getElementById('closureSummary').value;
        data.closureReason = document.getElementById('closureReason').value;
      } else if (type === 'followup') {
        data.followUpDetails = document.getElementById('followUpDetails').value;
      } else if (type === 'feedback') {
        data.feedbackPrompt = document.getElementById('feedbackPrompt').value;
      }

      // Send AJAX request to save update
      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'save_case_update.php', true);
      xhr.setRequestHeader('Content-Type', 'application/json');
      xhr.onreadystatechange = () => {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
          alert('Update saved successfully!');
          location.reload(); // Reload to show the updated data
        }
      };
      xhr.send(JSON.stringify(data));
    });
  });
</script>


<script>
    function openUpdateModal(id, name, category, description, fileDate, police) {
        // Populate the modal fields with the data
        document.getElementById('reportId').value = id;
        document.getElementById('modalName').textContent = name;
        document.getElementById('modalCategory').textContent = category;
        document.getElementById('modalDescription').textContent = description;
        document.getElementById('modalFileDate').textContent = fileDate;
        document.getElementById('modalPolice').textContent = police;

        // Show the modal
        var updateModal = new bootstrap.Modal(document.getElementById('updateModal'));
        updateModal.show();
    }

    document.getElementById('saveUpdateBtn').addEventListener('click', function() {
        const id = document.getElementById('reportId').value;
        const name = document.getElementById('modalName').textContent;
        const category = document.getElementById('modalCategory').textContent;
        const description = document.getElementById('modalDescription').textContent;
        const fileDate = document.getElementById('modalFileDate').textContent;
        const police = document.getElementById('modalPolice').textContent;

        const data = {
            id,
            name,
            category,
            description,
            fileDate,
            finish
        };

        // Example: Send data via fetch
        fetch('update_case.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        })
        .then(response => response.json())
        .then(result => {
            alert('Update successful');
            location.reload(); // Reload the page to reflect changes
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Update failed');
        });
    });
</script>




</body>

</html>
