<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "dbcon.php";

// Check database connection
if (!$conn) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Fetch markers from the emergency table where status is not 'Respond' or is NULL
$sql = "SELECT id, lat, location FROM emergency WHERE status != 'Respond' OR status IS NULL"; // Update the SQL query
$result = pg_query($conn, $sql);

if (!$result) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Query failed: ' . pg_last_error($conn)]);
    exit();
}

$markers = [];
while ($row = pg_fetch_assoc($result)) {
    $location = explode(',', $row['lat']);
    if (count($location) == 2) {
        $lat = trim($location[0]);
        $lng = trim($location[1]);
        $markers[] = [
            'id' => $row['id'],
            'lat' => $lat,
            'lng' => $lng,
            'location' => $row['location'] // Add the location details to the response
        ];
    }
}

header('Content-Type: application/json');
echo json_encode(['markers' => $markers]);
