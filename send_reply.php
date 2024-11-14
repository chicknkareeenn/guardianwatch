<?php
session_start();
include "dbcon.php"; // Ensure you are connecting to PostgreSQL database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the notification ID and the reply message from the POST request
    $notificationId = $_POST['notification_id'];
    $replyMessage = $_POST['reply_message'];
    
    // Retrieve the police ID from the session
    $policeId = $_SESSION['id'];

    // Prepare the query
    $sql = "INSERT INTO replies (notification_id, sender_role, message) VALUES ($1, $2, $3)";
    
    // Prepare the PostgreSQL statement
    $stmt = pg_prepare($conn, "insert_reply", $sql);

    // Execute the prepared statement with parameters
    $result = pg_execute($conn, "insert_reply", array($notificationId, 'police', $replyMessage));

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => pg_last_error($conn)]);
    }
}

// Close the PostgreSQL connection
pg_close($conn);
?>
