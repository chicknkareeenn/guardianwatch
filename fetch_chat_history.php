<?php
session_start();
include "dbcon.php";

$notification_id = $_GET['notification_id'];

// Fetch the notification message
$sql_notification = "SELECT notifications.notif, residents.fullname 
                     FROM notifications 
                     JOIN residents ON notifications.userId = residents.id 
                     WHERE notifications.notif_id = ?";
$stmt = $conn->prepare($sql_notification);
$stmt->bind_param("i", $notification_id);
$stmt->execute();
$notification_result = $stmt->get_result();
$notification_data = $notification_result->fetch_assoc();

// Fetch the chat messages
$sql_messages = "SELECT message, sender_role FROM replies WHERE notification_id = ? ORDER BY sent_at ASC";
$stmt = $conn->prepare($sql_messages);
$stmt->bind_param("i", $notification_id);
$stmt->execute();
$messages_result = $stmt->get_result();
$messages = [];
while ($msg = $messages_result->fetch_assoc()) {
    $messages[] = $msg;
}

$response = [
    'notif' => $notification_data['notif'],
    'fullname' => $notification_data['fullname'],
    'messages' => $messages
];

echo json_encode($response);
?>
