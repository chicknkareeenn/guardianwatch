<?php
session_start();
include "dbcon.php";

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set the content type to JSON
header('Content-Type: application/json');

// Get notification ID
$notification_id = $_GET['notification_id'];

// Check if notification_id is present
if (!isset($notification_id) || empty($notification_id)) {
    echo json_encode(["error" => "Notification ID is missing or invalid"]);
    exit();
}

// Fetch the notification message
$sql_notification = "SELECT notifications.notif, residents.fullname 
                     FROM notifications 
                     JOIN residents ON notifications.userId = residents.id 
                     WHERE notifications.notif_id = $1";

// Use pg_prepare and pg_execute for PostgreSQL
$stmt_notification = pg_prepare($conn, "fetch_notification", $sql_notification);
if (!$stmt_notification) {
    echo json_encode(["error" => "Error preparing notification query"]);
    exit();
}

$result_notification = pg_execute($conn, "fetch_notification", array($notification_id));

if (!$result_notification) {
    echo json_encode(["error" => "Error executing notification query"]);
    exit();
}

$notification_data = pg_fetch_assoc($result_notification);

// Fetch the chat messages
$sql_messages = "SELECT message, sender_role FROM replies WHERE notification_id = $1 ORDER BY sent_at ASC";

$stmt_messages = pg_prepare($conn, "fetch_messages", $sql_messages);
if (!$stmt_messages) {
    echo json_encode(["error" => "Error preparing messages query"]);
    exit();
}

$result_messages = pg_execute($conn, "fetch_messages", array($notification_id));

if (!$result_messages) {
    echo json_encode(["error" => "Error executing messages query"]);
    exit();
}

$messages = [];
while ($msg = pg_fetch_assoc($result_messages)) {
    $messages[] = $msg;
}

// Construct the response
$response = [
    'notif' => $notification_data['notif'],
    'fullname' => $notification_data['fullname'],
    'messages' => $messages
];

// Output the response as JSON
echo json_encode($response);
?>
