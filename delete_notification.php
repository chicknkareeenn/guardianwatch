<?php
session_start();
include "dbcon.php"; // Include your PostgreSQL database connection file

if (!isset($_SESSION['role']) || (trim($_SESSION['role']) == '')) {
    header('location:main.php');
    exit();
}

if (isset($_POST['id'])) {
    $notificationId = intval($_POST['id']); // Get the notification ID from the POST request

    // Prepare the SQL statement to delete the notification
    $sql = "DELETE FROM notifications WHERE id = $1"; // PostgreSQL parameterized query

    // Prepare the statement
    $stmt = pg_prepare($conn, "delete_notification", $sql);

    // Execute the prepared statement with the notification ID
    $result = pg_execute($conn, "delete_notification", array($notificationId));

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => pg_last_error($conn)]);
    }
}
pg_close($conn); // Close the PostgreSQL connection
?>
