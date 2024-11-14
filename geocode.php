<?php
session_start();
include "dbcon.php";  // Assuming dbcon.php sets up the PostgreSQL connection

// Check session role
if (!isset($_SESSION['role']) || (trim($_SESSION['role']) == '')) {
    header('location:main.php');
    exit();
}

function getCoordinates($address) {
    $address = urlencode($address);
    $url = "https://nominatim.openstreetmap.org/search?q=$address&format=json&addressdetails=1";

    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
        return [
            'lat' => $data[0]['lat'],
            'lng' => $data[0]['lon']
        ];
    }

    return null;
}

// Fetch emergency locations from the database
$sql = "SELECT location FROM emergency";
$result = pg_query($conn, $sql);

$locations = [];
while ($row = pg_fetch_assoc($result)) {
    $coordinates = getCoordinates($row['location']);
    if ($coordinates) {
        $locations[] = $coordinates;
    }
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode(['markers' => $locations]);
?>
