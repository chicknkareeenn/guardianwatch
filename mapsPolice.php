<?php 
session_start();
include "dbcon.php";

// Check session role
if (!isset($_SESSION['role']) || (trim($_SESSION['role']) == '')) {
    header('location:main.php');
    exit();
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
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">

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
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />

    <!-- jQuery (required for $ AJAX call) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom CSS for Map Container -->
    <style>
        #map {
            height: 80vh;
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
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.locatecontrol/dist/L.Control.Locate.min.css" />
    <script src="https://unpkg.com/leaflet.locatecontrol/dist/L.Control.Locate.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize the map
            const map = L.map('map').setView([14.0688, 120.6289], 13);

            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            
            L.control.locate().addTo(map);

            const fixedLocationIcon = L.icon({
                iconUrl: 'locate.png', // Path to your marker icon
                iconSize: [40, 40],
                iconAnchor: [25, 50],
                popupAnchor: [0, -50],
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                shadowSize: [50, 64]
            });

            const fixedMarker = L.marker([14.0688, 120.6289], { icon: fixedLocationIcon }).addTo(map);
            fixedMarker.bindPopup("<b>Your Location:</b><br>J P Laurel St, Nasugbu, Batangas, Philippines").openPopup();

            // Define custom icons
            const blueCircleIcon = L.icon({
                iconUrl: 'marker3.gif', // Path to your GIF marker icon
                iconSize: [50, 50], // Size of the icon
                iconAnchor: [20, 40], // Point of the icon which will correspond to marker's location
                popupAnchor: [1, -34], // Point from which the popup should open relative to the iconAnchor
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                shadowSize: [41, 41] // Size of the shadow
            });

            // Store markers in an object with their IDs as keys
            const markerLayers = {};
            let routingControl;

            // Function to fetch markers from the backend
            const fetchMarkers = () => {
    // Remove existing routing control if it exists
    if (routingControl) {
        map.removeControl(routingControl);
        routingControl = null; // Clear the reference to the routing control
    }

    $.ajax({
        url: 'emergencymark.php', // URL of the PHP script that fetches marker data
        method: 'GET',
        dataType: 'json',
        success: (data) => {
            data.markers.forEach(marker => {
                if (markerLayers[marker.id]) {
                    // Update existing marker position if necessary
                    markerLayers[marker.id].setLatLng([marker.lat, marker.lng]);
                } else {
                    // Create a new marker with blue circle icon
                    const newMarker = L.marker([marker.lat, marker.lng], { icon: blueCircleIcon }).addTo(map);
                    
                    // Bind a popup to the marker with the location details
                    newMarker.bindPopup(`
                        <b>Location Details:</b><br>${marker.location}<br>
                        <button class="respond-button" data-marker-id="${marker.id}">Respond</button>
                    `).on('click', (e) => {
                        const latLng = e.target.getLatLng();

                        const fixedStartLatLng = L.latLng(14.0688, 120.6289);
                        // Get current location
                        if (routingControl) {
                                map.removeControl(routingControl);
                            }

                            routingControl = L.Routing.control({
                                waypoints: [
                                    fixedStartLatLng,
                                    L.latLng(latLng.lat, latLng.lng)
                                ],
                                routeWhileDragging: true
                            }).addTo(map);
                        });

                        markerLayers[marker.id] = newMarker;
                    }
                });

                for (let id in markerLayers) {
                    if (!data.markers.find(marker => marker.id == id)) {
                        map.removeLayer(markerLayers[id]);
                        delete markerLayers[id];
                    }
                }
            },
            error: (xhr, status, error) => {
                console.error('Error fetching marker data:', error);
            }
        });
    };

    fetchMarkers();
    setInterval(fetchMarkers, 30000);
});
    </script>

    <script>
        $(document).on('click', '.respond-button', function() {
            const markerId = $(this).data('marker-id');
            // Optional: Indicate loading state
            $(this).text('Updating...').attr('disabled', true); // Disable button and change text

            $.ajax({
                url: 'updateEmergencyStatus.php', // URL to the PHP script that updates the status
                method: 'POST',
                data: { id: markerId, status: 'Respond' },
                success: (response) => {
                    // Handle success, maybe show a message or refresh markers
                    alert('Emergency status updated to Respond!');
                    fetchMarkers(); // Refresh markers after updating status
                },
                error: (xhr, status, error) => {
                    console.error('Error updating status:', error);
                    alert('Failed to update status. Please try again.');
                },
                complete: () => {
                    // Optional: Reset the button after completion
                    $(this).text('Respond').attr('disabled', false); // Reset button
                }
            });
        });
    </script>
</body>
</html>
