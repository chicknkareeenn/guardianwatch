<?php
session_start();
include "dbcon.php";

if (!isset($_SESSION['role']) || (trim($_SESSION['role']) == '')) {
    header('location:index.php');
    exit();
}

$selected_year = isset($_POST['year']) ? intval($_POST['year']) : date("Y");

// Query to count new reports for the selected year
$sql = "SELECT COUNT(*) AS count FROM reports WHERE finish IS NULL AND EXTRACT(YEAR FROM file_date) = $selected_year";
$result = pg_query($conn, $sql);

$count = ($result) ? pg_fetch_result($result, 0, 'count') : 0;

// Query to count available police
$sql_police = "SELECT COUNT(*) AS police_count FROM police WHERE status = 'Available'";
$result_police = pg_query($conn, $sql_police);

$police_count = ($result_police) ? pg_fetch_result($result_police, 0, 'police_count') : 0;

// Query to count ongoing cases for the selected year
$sql_cases = "SELECT COUNT(*) AS ongoing_count FROM reports WHERE finish = 'Ongoing' AND EXTRACT(YEAR FROM file_date) = $selected_year";
$result_cases = pg_query($conn, $sql_cases);

$ongoing_count = ($result_cases) ? pg_fetch_result($result_cases, 0, 'ongoing_count') : 0;

// Query to get total crimes by category for the selected year
$sql_category = "SELECT category, COUNT(*) AS category_count FROM reports WHERE EXTRACT(YEAR FROM file_date) = $selected_year GROUP BY category";
$result_category = pg_query($conn, $sql_category);
$category_data = [];
$category_labels = [];
$category_counts = [];

while ($row_category = pg_fetch_assoc($result_category)) {
    $category_labels[] = $row_category['category'];
    $category_counts[] = $row_category['category_count'];
}

// Query to get total crimes reported by month for the selected year
$sql_monthly = "
SELECT 
    TO_CHAR(file_date, 'YYYY-Mon') AS month,  -- Keep the month in 'YYYY-Mon' format
    EXTRACT(MONTH FROM file_date) AS month_num,  -- Extract the month number for sorting
    COUNT(*) AS total_crimes
FROM 
    reports
WHERE 
    EXTRACT(YEAR FROM file_date) = $selected_year
GROUP BY 
    month, month_num  -- Group by both month and month_num
ORDER BY 
    month_num  -- Sort by the extracted month number
";
$result_monthly = pg_query($conn, $sql_monthly);

$monthly_data = [];
$monthly_labels = [];

if ($result_monthly) {
    while ($row_monthly = pg_fetch_assoc($result_monthly)) {
        $monthly_labels[] = $row_monthly['month']; // Store month in YYYY-MMM format
        $monthly_data[] = $row_monthly['total_crimes']; // Store the count of crimes
    }
}

// Query to get emergency locations for the selected year
$sql_emergency = "SELECT location, lat FROM emergency WHERE EXTRACT(YEAR FROM report_date) = $selected_year";
$result_emergency = pg_query($conn, $sql_emergency);

$emergency_locations = [];
if ($result_emergency) {
    while ($row_emergency = pg_fetch_assoc($result_emergency)) {
        $lat = explode(',', $row_emergency['lat']);
        if (count($lat) == 2) {
            $emergency_locations[] = [
                'location' => $row_emergency['location'],
                'lat' => (float)$lat[0],
                'long' => (float)$lat[1],
            ];
        }
    }
}

$male_query = "SELECT COUNT(*) as male_count FROM reports WHERE EXTRACT(YEAR FROM file_date) = $selected_year and gender = 'Male'";
$female_query = "SELECT COUNT(*) as female_count FROM reports WHERE EXTRACT(YEAR FROM file_date) = $selected_year and gender = 'Female'";

$male_result = pg_query($conn, $male_query);
$female_result = pg_query($conn, $female_query);

if (!$male_result || !$female_result) {
    die("Error in query execution: " . pg_last_error());
}

$male_reports_count = pg_fetch_result($male_result, 0, 'male_count');
$female_reports_count = pg_fetch_result($female_result, 0, 'female_count');


?>
<?php
// Query to get the most reported crime category
$brgyquery = "SELECT category, COUNT(*) AS category_count
          FROM reports
          WHERE EXTRACT(YEAR FROM file_date) = $selected_year
          GROUP BY category
          ORDER BY category_count DESC
          LIMIT 1"; // Fetches the most reported category
$result = pg_query($conn, $brgyquery);

// Get the category with the highest count
$most_reported_category = pg_fetch_assoc($result)['category'];

// Query to get the distribution of the most reported category by barangay
$query_barangay = "SELECT address, COUNT(*) AS count
                   FROM reports
                   WHERE category = '$most_reported_category' and EXTRACT(YEAR FROM file_date) = $selected_year
                   GROUP BY address
                   ORDER BY count DESC
                   LIMIT 3";
$result_barangay = pg_query($conn, $query_barangay);

// Prepare the data for the Donut Chart
$barangay_data = [];
while ($row = pg_fetch_assoc($result_barangay)) {
    $barangay_data[] = ['address' => $row['address'], 'count' => $row['count']];
}

// Encode data to be used in JavaScript
$barangay_json = json_encode($barangay_data);
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOMiW0t0z5A4uY8Q5ZO1+QAc2P5p/dR9u5ViB3" crossorigin="anonymous">


  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <style>
    .card {
      margin-bottom: 15px; /* Space between cards */
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
    #map { height: 70vh; }
    .modal-content h2 i {
      margin-right: 18px;
      color: #f0ad4e;
    }
    .pagetitle {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .year-filter {
      display: flex;
      align-items: center;
      margin-left: 20px;
    }

    .year-filter label {
      margin-right: 10px;
      font-size: 20px;
      font-weight: bold;
      color: #184965;
    }

    .dropdown-container {
      display: inline-block; /* Ensures the dropdown is inline with the label */
    }
    #clock {
      font-size: 20px;
      font-weight: bold;
      color: #184965;
      margin: 0 15px;
      /* Additional styles to resemble a cellphone clock */
    }

    
  </style>
</head>

<body>
  <?php include "nav1.php"; ?>

  <main id="main" class="main">
    <audio id="alertSound" src="alert.mp3" preload="auto"></audio>

    <div class="pagetitle">
      <h1>Dashboard</h1>

      <div style="display: flex; align-items: center;"> 
          <i class="fas fa-clock" style="font-size: 20px; color: #184965; margin-right: -5px;"></i>
          <div id="clock" style="font-size: 20px; font-weight: bold; color: #184965; margin-right: 15px;"></div>

          <i class="fas fa-calendar-alt" style="font-size: 20px; color: #184965; margin-right: 7px; margin-left:100px;"></i>
          <div id="date" style="font-size: 20px; font-weight: bold; color: #184965;"></div>
      </div>

      <div class="year-filter">
            <i class="fas fa-filter" style="font-size: 17px; color: #184965; margin-right: 7px;"></i>
            <label for="year">Filter Year:</label>
            <form method="POST" action="">
              <select name="year" id="year" onchange="this.form.submit();">
                <?php for ($i = 2000; $i <= date("Y"); $i++): ?>
                  <option value="<?php echo $i; ?>" <?php if ($i == $selected_year) echo 'selected'; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
              </select>
            </form>
          </div>
        </div>
    </div>


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
                    <i class="bi bi-file-earmark-plus" style="color: #184965;"></i>
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
                    <i class="bi bi-hourglass-split" style="color: #184965;"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo $ongoing_count; ?></h6>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>
          <div class="col-xxl-3 col-md-6">
            <div class="card info-card sales-card">
              <div class="card-body">
                <h5 class="card-title" style="font-size: 14px;">Gender Distribution of Reports</h5>
                <div class="d-flex align-items-center justify-content-center" style="margin-top: -20px;">
                  <canvas id="genderPieChart" style="max-width: 180px; max-height: 89px;"></canvas>
                </div>
              </div>
            </div>
          </div>
          <!-- Emergency Card -->
          <div class="col-xxl-3 col-md-6">
            <div class="card info-card sales-card">
                <div class="card-body">
                    <h5 class="card-title" style="font-size: 14px;">Top Crime: <?php echo $most_reported_category; ?></h5>
                    <div class="d-flex align-items-center justify-content-center" style="margin-top: -20px;">
                        <canvas id="categoryDonutChart" style="max-width: 180px; max-height: 89px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section dashboard">
      <div class="container">
        <div class="row">
          <div class="col-xxl-6 col-md-6">
            <div class="card info-card sales-card">
              <div class="card-body">
                <canvas id="barChart" style="width:100%;max-width:600px"></canvas>
              </div>
            </div>
          </div>
          <div class="col-xxl-6 col-md-6">
            <div class="card info-card sales-card">
              <div class="card-body">
                <canvas id="lineChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="section dashboard">
      <div class="container">
        <div class="row">
          <div class="col-xxl-12 col-md-12">
            <div class="card info-card sales-card">
              <div class="card-body">
                <center><h5 class="card-title">Geographic Density of Emergency Reports</h5></center>
                <div id="map"></div></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
   
 
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
  <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet.locatecontrol/dist/L.Control.Locate.min.css" />
    <script src="https://unpkg.com/leaflet.locatecontrol/dist/L.Control.Locate.min.js"></script>

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
  const ws = new WebSocket('wss://reporting-9wym.onrender.com');

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
  // Initialize the map
  const emergencyLocations = <?php echo json_encode($emergency_locations); ?>;
  const map = L.map('map').setView([14.06702850055952, 120.62615777059939], 16);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    const heatmapData = emergencyLocations.map(location => [location.lat, location.long]);
    const heat = L.heatLayer(heatmapData, { radius: 25, blur: 15 }).addTo(map);
    

    const lineChartCtx = document.getElementById('lineChart').getContext('2d');
    const lineChart = new Chart(lineChartCtx, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($monthly_labels); ?>,
        datasets: [{
          label: 'Total Crimes',
          data: <?php echo json_encode($monthly_data); ?>,
          backgroundColor: 'rgba(75, 192, 192, 0.2)', // Adjust to match your color
          borderColor: '#184965',
          borderWidth: 3,
          fill: true,
          tension: 0.1, // Smooth the line
          pointRadius: 5, // Adjust point size for better visibility
          pointHoverRadius: 7,
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: true,
            position: 'top',
          },
          title: {
            display: true,
            text: 'Monthly Crime Statistics for Year <?php echo $selected_year; ?>',
            font: {
                    size: 16 // Font size for the title
                },
                color: '#333'
          }
        },
        scales: {
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

    // Bar Chart Data
    const barChartCtx = document.getElementById('barChart').getContext('2d');
    const barChart = new Chart(barChartCtx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($category_labels); ?>,
        datasets: [{
          label: 'Crimes by Category',
          data: <?php echo json_encode($category_counts); ?>,
          backgroundColor: '#184965', // Bar color
          borderColor: '#184965', // Bar border color
          borderWidth: 1
        }]
      },
      options: {
      scales: {
        y: {
          beginAtZero: true // Start y-axis at 0
        }
      },
      scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Crime Reports for Year <?php echo $selected_year; ?>'
          }
        },
        
          y: {
            beginAtZero: true
          }
      },
      plugins: {
            title: {
                display: true,
                text: 'Crime Reports by Category', // Title for the bar chart
                font: {
                    size: 16 // Font size for the title
                },
                color: '#333'
            }
        }
    }
});

document.addEventListener('DOMContentLoaded', function () {
  const ctx = document.getElementById('genderPieChart').getContext('2d');
  new Chart(ctx, {
    type: 'pie',
    data: {
      labels: ['Male', 'Female'],
      datasets: [{
        data: [
          <?php echo $male_reports_count; ?>,
          <?php echo $female_reports_count; ?>
        ],
        backgroundColor: ['#184965', '#FFD700'],
        borderColor: ['#FFFFFF', '#FFFFFF'],
        borderWidth: 1
      }]
    },
    options: {
      plugins: {
        legend: {
          position: 'right', // Moves legend to the right side
          labels: {
            boxWidth: 10, // Adjust width of the color box
            boxHeight: 10, // Adjust height of the color box
            font: {
              size: 10 // Adjust font size for better fit
            }
          }
        }
      },
      responsive: true,
    }
  });
});
  </script>

<script>
  // Function to update time and date
function updateClock() {
    const now = new Date();
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const formattedDate = now.toLocaleDateString(undefined, options);
    
    // Get current time
    const currentTime = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    
    // Update the clock and date elements
    document.getElementById('clock').textContent = currentTime;
    document.getElementById('date').textContent = formattedDate;
}

// Update clock and date every second
setInterval(updateClock, 1000);

// Initial call to display immediately on load
updateClock();

</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Get the barangay distribution data passed from PHP
    const barangayData = <?php echo $barangay_json; ?>;
    
    // Extract the barangay names (addresses) and counts for the Donut chart
    const labels = barangayData.map(data => data.address);  // Get addresses (barangays)
    const data = barangayData.map(data => data.count);      // Get counts of reports

    const ctx = document.getElementById('categoryDonutChart').getContext('2d');
    
    // Create the Donut Chart
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: ['#FF5733', '#33FF57', '#FFD700', '#184965', '#33B5FF'], // Dynamic colors for each barangay
                borderColor: ['#fff', '#fff', '#fff', '#fff', '#fff'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                    boxWidth: 10, // Adjust width of the color box
                    boxHeight: 10, // Adjust height of the color box
                    font: {
                      size: 10 // Adjust font size for better fit
                    }
                  }
                }
            }
        }
    });
});
</script>



</body>
</html>
