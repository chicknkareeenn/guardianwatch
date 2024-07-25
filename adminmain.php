<?php
session_start();
include "dbcon.php";

if (!isset($_SESSION['role']) || (trim($_SESSION['role']) == '')) {
    header('location:index.php');
    exit();
}

// Query to count reports where finish is 'Unsettled'
$sql = "SELECT COUNT(*) AS count FROM reports WHERE finish = ''";
$result = mysqli_query($conn, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $count = $row['count'];
} else {
    $count = 0;
}

// Query to count all available police
$sql_police = "SELECT COUNT(*) AS police_count FROM police where status = 'Available'";
$result_police = mysqli_query($conn, $sql_police);

if ($result_police) {
    $row_police = mysqli_fetch_assoc($result_police);
    $police_count = $row_police['police_count'];
} else {
    $police_count = 0;
}

// Query to count ongoing cases
$sql_cases = "SELECT COUNT(*) AS ongoing_count FROM reports WHERE finish = 'Ongoing'";
$result_cases = mysqli_query($conn, $sql_cases);

if ($result_cases) {
    $row_cases = mysqli_fetch_assoc($result_cases);
    $ongoing_count = $row_cases['ongoing_count'];
} else {
    $ongoing_count = 0;
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Crime Reports</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="logooo.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">

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

  <style>
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
      color: #f0ad4e;
    }
    #bubbleChart {
      width: 100% !important;
      height: 400px;
    }
    #lineChart {
      width: 102% !important;
      height: 210px;
      margin-left: 13px;
    }
  </style>
</head>

<body>
  <?php include "nav1.php"; ?>

  <main id="main" class="main">
    <audio id="alertSound" src="alert.mp3" preload="auto"></audio>

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav></nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="container">
        <div class="row">
          <!-- New Reports Card -->
          <div class="col-xxl-3 col-md-6">
            <a href="adminreport.php">
              <div class="card info-card customers-card">
                <div class="card-body">
                  <h5 class="card-title">New Reports</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-person-lines-fill"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo $count; ?></h6>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>
    <!-- Sales Card -->

          <!-- Ongoing Cases Card -->
          <div class="col-xxl-3 col-md-6">
            <a href="adminoncase.php">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Ongoing Cases</h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-houses"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo $ongoing_count; ?></h6>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>

          <div class="col-xxl-6 col-md-6">

          <canvas id="barChart" style="height: 50px; width:-7%; border-radius: 5px; box-shadow: 0px 0 30px rgba(1, 41, 112, 0.1);"></canvas>
                
                    </div>

    <!-- Customers Card 1 -->

    <div class="row">
            <div class="col-6">
              <div id="map" style="height: 420px; width:102%; margin-top: -100px;border-radius: 5px; box-shadow: 0px 0 30px rgba(1, 41, 112, 0.1);"></div>
            </div>

            <div class="col-6">
              <canvas id="lineChart" style="height: 315px; width: 50%;border-radius: 5px; margin-top: 19px; box-shadow: 0px 0 30px rgba(1, 41, 112, 0.1);"></canvas>
            </div>
            
          </div>
   
  </div>
</div>
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
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
  <script>
  const alertSound = document.getElementById('alertSound');
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
    utterance.rate = 1.1; // Normal speech rate (default is 1)
    utterance.pitch = 1.5; // Higher pitch (default is 1)
    utterance.volume = 5; // Max volume (default is 1)
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
    alertSound.pause(); // Stop the alert sound
    alertSound.currentTime = 0; // Reset playback position to start
    window.speechSynthesis.cancel(); // Cancel ongoing speech synthesis
  };

    // Close the modal if the user clicks anywhere outside of the modal content
    window.onclick = function(event) {
    if (event.target === modal) {
      modal.style.display = 'none';
      alertSound.pause(); // Stop the alert sound
      alertSound.currentTime = 0; // Reset playback position to start
      window.speechSynthesis.cancel(); // Cancel ongoing speech synthesis
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
    document.addEventListener('DOMContentLoaded', () => {
      const map = L.map('map').setView([51.505, -0.09], 13);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
      }).addTo(map);

      const crimeData = [
        { lat: 51.505, lng: -0.09, crimeRate: 10, location: "Location A" },
        { lat: 51.51, lng: -0.1, crimeRate: 20, location: "Location B" },
        { lat: 51.49, lng: -0.08, crimeRate: 30, location: "Location C" },
        { lat: 51.48, lng: -0.1, crimeRate: 40, location: "Location D" },
        { lat: 51.49, lng: -0.1, crimeRate: 50, location: "Location E" },
        { lat: 51.52, lng: -0.09, crimeRate: 60, location: "Location F" },
        { lat: 51.50, lng: -0.11, crimeRate: 70, location: "Location G" },
        { lat: 51.48, lng: -0.07, crimeRate: 80, location: "Location H" },
        { lat: 51.47, lng: -0.12, crimeRate: 90, location: "Location I" },
        { lat: 51.53, lng: -0.10, crimeRate: 100, location: "Location J" },
        { lat: 51.54, lng: -0.08, crimeRate: 110, location: "Location K" },
        { lat: 51.55, lng: -0.07, crimeRate: 120, location: "Location L" },
        { lat: 51.56, lng: -0.09, crimeRate: 130, location: "Location M" },
        { lat: 51.57, lng: -0.1, crimeRate: 140, location: "Location N" },
        { lat: 51.58, lng: -0.11, crimeRate: 150, location: "Location O" },
        { lat: 51.59, lng: -0.12, crimeRate: 160, location: "Location P" },
        { lat: 51.60, lng: -0.13, crimeRate: 170, location: "Location Q" },
        { lat: 51.61, lng: -0.14, crimeRate: 180, location: "Location R" },
        { lat: 51.62, lng: -0.15, crimeRate: 190, location: "Location S" },
        { lat: 51.63, lng: -0.16, crimeRate: 200, location: "Location T" },
        { lat: 51.64, lng: -0.17, crimeRate: 210, location: "Location U" },
        { lat: 51.65, lng: -0.18, crimeRate: 220, location: "Location V" },
        { lat: 51.66, lng: -0.19, crimeRate: 230, location: "Location W" },
        { lat: 51.67, lng: -0.20, crimeRate: 240, location: "Location X" },
        { lat: 51.68, lng: -0.21, crimeRate: 250, location: "Location Y" },
        { lat: 51.69, lng: -0.22, crimeRate: 260, location: "Location Z" },
        { lat: 51.70, lng: -0.23, crimeRate: 270, location: "Location AA" },
        { lat: 51.71, lng: -0.24, crimeRate: 280, location: "Location BB" },
        { lat: 51.72, lng: -0.25, crimeRate: 290, location: "Location CC" },
        { lat: 51.73, lng: -0.26, crimeRate: 300, location: "Location DD" },
        { lat: 51.74, lng: -0.27, crimeRate: 310, location: "Location EE" },
        { lat: 51.75, lng: -0.28, crimeRate: 320, location: "Location FF" },
        { lat: 51.76, lng: -0.29, crimeRate: 330, location: "Location GG" },
        { lat: 51.77, lng: -0.30, crimeRate: 340, location: "Location HH" },
        { lat: 51.78, lng: -0.31, crimeRate: 350, location: "Location II" },
        { lat: 51.79, lng: -0.32, crimeRate: 360, location: "Location JJ" },
        { lat: 51.80, lng: -0.33, crimeRate: 370, location: "Location KK" },
        { lat: 51.81, lng: -0.34, crimeRate: 380, location: "Location LL" },
        { lat: 51.82, lng: -0.35, crimeRate: 390, location: "Location MM" },
        { lat: 51.83, lng: -0.36, crimeRate: 400, location: "Location NN" },
        { lat: 51.84, lng: -0.37, crimeRate: 410, location: "Location OO" },
        { lat: 51.85, lng: -0.38, crimeRate: 420, location: "Location PP" },
        { lat: 51.86, lng: -0.39, crimeRate: 430, location: "Location QQ" },
        { lat: 51.87, lng: -0.40, crimeRate: 440, location: "Location RR" },
        { lat: 51.88, lng: -0.41, crimeRate: 450, location: "Location SS" },
        { lat: 51.89, lng: -0.42, crimeRate: 460, location: "Location TT" },
        { lat: 51.90, lng: -0.43, crimeRate: 470, location: "Location UU" },
        { lat: 51.91, lng: -0.44, crimeRate: 480, location: "Location VV" },
        { lat: 51.92, lng: -0.45, crimeRate: 490, location: "Location WW" },
        { lat: 51.93, lng: -0.46, crimeRate: 500, location: "Location XX" }
      ];

      const maxCrimeRate = Math.max(...crimeData.map(d => d.crimeRate));
      const scaleFactor = 50 / maxCrimeRate;

      crimeData.forEach(data => {
        const circle = L.circle([data.lat, data.lng], {
          color: 'red',
          fillColor: '#f03',
          fillOpacity: 0.5,
          radius: data.crimeRate * scaleFactor * 50
        }).addTo(map);

        circle.bindPopup(`Location: ${data.location}<br>Crime Rate: ${data.crimeRate}`);
      });

      const lineChartData = {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [{
          label: 'Number of Reports',
          data: [65, 59, 80, 81, 56, 55, 40],
          borderColor: '#184965',
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          fill: true,
          tension: 0.1
        }]
      };

      new Chart(document.getElementById('lineChart').getContext('2d'), {
        type: 'line',
        data: lineChartData,
        options: {
          responsive: true,
          plugins: {
            title: {
              display: true,
              text: 'Monthly Number of Reports',
              font: {
                size: 16
              },
              color: '#333'
            }
          },
          scales: {
            x: {
              beginAtZero: true
            },
            y: {
              beginAtZero: true
            }
          }
        }
      });
    });

    const locations = ['Robbery', 'Assault', 'Burglary', 'Vandalism', 'Theft'];
    const categories = ['Banilad', 'Bucana', 'Aga', 'Barangay 1', 'Barangay 2'];

    // Example data structure: total reports per category per location
    const data = {
      'Robbery': [10, 20, 30, 15, 25],
      'Assault': [20, 30, 25, 10, 20],
      'Burglary': [15, 25, 30, 20, 35],
      'Vandalism': [30, 15, 20, 25, 10],
      'Theft': [25, 35, 20, 30, 15]
    };

    // Define a set of darker appealing colors
    const colors = [
  'rgba(135, 206, 250, 0.8)',  // Light Sky Blue
  'rgba(70, 130, 180, 0.8)',   // Steel Blue
  'rgba(0, 77, 153, 0.8)',    // Dark Steel Blue
  'rgba(0, 0, 139, 0.8)',      // Dark Blue
  'rgba(25, 25, 112, 0.8)'     // Midnight Blue
];

const borderColors = [
  'rgba(135, 206, 250, 1)',  // Light Sky Blue
  'rgba(70, 130, 180, 1)',   // Steel Blue
  'rgba(0, 77, 153, 0.8)',    // Dark Steel Blue
  'rgba(0, 0, 139, 1)',      // Dark Blue
  'rgba(25, 25, 112, 1)'     // Midnight Blue
];

    // Prepare datasets for each location with updated colors
    const datasets = locations.map((location, index) => ({
      label: location,
      data: data[location],
      backgroundColor: colors[index],
      borderColor: borderColors[index],
      borderWidth: 1
    }));

    // Initialize the horizontal bar chart with updated colors
    new Chart(document.getElementById('barChart').getContext('2d'), {
      type: 'bar',
      data: {
        labels: categories,
        datasets: datasets
      },
      options: {
        indexAxis: 'y', // This makes the bars horizontal
        responsive: true,
        plugins: {
          legend: {
            position: 'top'
          },
          title: {
            display: true,
            text: 'Total Reported Crimes by Category and Location',
            font: {
                size: 16
              },
              color: '#333'
          }
        },
        scales: {
          x: {
            stacked: true,
            beginAtZero: true
          },
          y: {
            stacked: true
          }
        }
      }
    });

  </script>



</body>
</html>
