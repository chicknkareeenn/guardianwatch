<?php
include "dbcon.php";

$category = $_GET['category'];

$sql = "SELECT id, fullname FROM police WHERE status != 'Not Available' AND category = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $category);
$stmt->execute();
$result = $stmt->get_result();

$police = array();
while ($row = $result->fetch_assoc()) {
    $police[] = $row;
}

echo json_encode($police);
?>
