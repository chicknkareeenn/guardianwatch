<?php 
session_start();
include "dbcon.php";

// Check session role
if (!isset($_SESSION['role']) || (trim($_SESSION['role']) == '')) {
    header('location:main.php');
    exit();
}

// Fetch emergency locations from the database
$sql = "SELECT location FROM emergency";
$result = mysqli_query($conn, $sql);
$locations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $locations[] = $row['location'];
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
    <link href="assets/css/style.css" rel="stylesheet">

    <!-- Leaflet CSS File -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <!-- Custom CSS for Map Container -->
    <style>
        #map {
            height: 70vh;
            width: 100%;
        }
    </style>
</head>

<body>
    <?php include "nav3.php"; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Real-time Map</h1>
            <nav></nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="col-12">
                <div class="card recent-sales overflow-auto">
                    <div class="card-body">
                        <div id="map"></div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Leaflet JS File -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        // Initialize the map and set view to a default location
        var map = L.map('map').setView([0, 0], 2);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Function to geocode a location and add a marker to the map
        function geocodeLocation(location) {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(location)}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        var latlng = [data[0].lat, data[0].lon];

                        // Custom red flag icon for markers
                        var redIcon = L.icon({
                            iconUrl: 'assets/img/red-flag.png',
                            iconSize: [32, 32],
                            iconAnchor: [16, 32],
                            popupAnchor: [0, -32]
                        });

                        // Add marker with custom icon
                        var marker = L.marker(latlng, { icon: redIcon }).addTo(map);
                        map.setView(latlng, 13);
                    } else {
                        console.error('Geocoding failed for location: ' + location);
                    }
                })
                .catch(error => console.error('Geocoding error: ', error));
        }

        // Array of emergency locations from the database
        var locations = <?php echo json_encode($locations); ?>;

        // Add markers for each location
        locations.forEach(location => {
            geocodeLocation(location);
        });

        // Geolocation function to get user's current position
        function onLocationFound(e) {
            var latlng = e.latlng;
            L.marker(latlng).addTo(map)
                .bindPopup("You are here").openPopup();
            map.setView(latlng, 13);
        }

        function onLocationError(e) {
            alert(e.message);
        }

        // Use the browser's geolocation API
        map.on('locationfound', onLocationFound);
        map.on('locationerror', onLocationError);

        map.locate({ setView: true, maxZoom: 16 });
    </script>
</body>

</html>
