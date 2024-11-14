<?php
session_start();
include 'dbcon.php';

// Check if the user is logged in and has the right role
if (!isset($_SESSION['role']) || (trim($_SESSION['role']) == '')) {
    header('location:index.php');
    exit();
}

// Query to get crime data near the specified location
$sql = "SELECT latitude AS lat, longitude AS lng, crime_rate AS crimeRate, location 
        FROM crimes WHERE ST_Distance_Sphere(POINT(longitude, latitude), POINT(120.62615777059939, 14.06702850055952)) < 5000"; // Within 5 km

$result = mysqli_query($conn, $sql);

$crimeData = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $crimeData[] = $row;
    }
}

mysqli_close($conn);

// Output the data in JSON format
header('Content-Type: application/json');
echo json_encode($crimeData);
?>
