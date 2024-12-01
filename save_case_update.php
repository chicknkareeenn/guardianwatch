<?php
// Include database connection
include('dbcon.php');

// Get the data from the AJAX request
$data = json_decode(file_get_contents('php://input'), true);
$reportId = $data['reportId'];
$type = $data['type'];

if ($type === 'status') {
    $status = $data['status'];

    // Assuming you have the userId from the report
    // Prepare and execute the query to fetch user_id and other data from the reports table
    $query = "SELECT user_id, name, category, description, police_assign, file_date FROM reports WHERE id = $1";
    $result = pg_prepare($conn, "fetch_report_data", $query);
    $result = pg_execute($conn, "fetch_report_data", array($reportId));

    if ($row = pg_fetch_assoc($result)) {
        $userId = $row['user_id'];
        $fullname = $row['name'];
        $category = $row['category'];
        $details = $row['description'];
        $police = $row['police_assign'];
        $file_date = $row['file_date']; 

        // Update status
        $updateQuery = "UPDATE reports SET finish = $1 WHERE id = $2";
        $updateStmt = pg_prepare($conn, "update_report_status", $updateQuery);
        $updateResult = pg_execute($conn, "update_report_status", array($status, $reportId));

        if (pg_affected_rows($updateResult) > 0) {
            // Prepare and execute the query to insert into the files table
            $notificationMessage = "Case status updated by police. Your case is now $status.";

            $notificationQuery = "
                INSERT INTO files (reportid, user_id, fullname, notes, police, category, details, file_date, time)
                VALUES ($1, $2, $3, $4, $5, $6, $7, $8, NOW())
            ";
            $notificationStmt = pg_prepare($conn, "insert_file_record", $notificationQuery);
            $notificationResult = pg_execute($conn, "insert_file_record", array($reportId, $userId, $fullname, $notificationMessage, $police, $category, $details, $file_date));

            if (pg_affected_rows($notificationResult) > 0) {
                echo json_encode(['status' => 'success', 'message' => 'Update saved and notification sent.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error inserting into files table.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error updating report status.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Report not found.']);
    }
} elseif ($type === 'schedule') {
    // If the update type is schedule (Interview/Court Dates)
    $scheduleDetails = $data['scheduleDetails'];
    $scheduleDate = $data['scheduleDate'];
    $scheduleTime = $data['scheduleTime'];

    // Fetch the necessary report data
    $query = "SELECT user_id, name, category, description, police_assign, file_date FROM reports WHERE id = $1";
    $result = pg_prepare($conn, "fetch_report_data_schedule", $query);
    $result = pg_execute($conn, "fetch_report_data_schedule", array($reportId));

    if ($row = pg_fetch_assoc($result)) {
        $userId = $row['user_id'];
        $fullname = $row['name'];
        $category = $row['category'];
        $details = $row['description'];
        $police = $row['police_assign'];
        $file_date = $row['file_date']; 

        // Prepare and execute the query to insert into the files table with schedule details
        $notificationMessage = "Schedule : $scheduleDetails on $scheduleDate at $scheduleTime.";

        $notificationQuery = "
            INSERT INTO files (reportid, user_id, fullname, notes, police, category, details, file_date, time)
            VALUES ($1, $2, $3, $4, $5, $6, $7, $8, NOW())
        ";
        $notificationStmt = pg_prepare($conn, "insert_schedule_file", $notificationQuery);
        $notificationResult = pg_execute($conn, "insert_schedule_file", array($reportId, $userId, $fullname, $notificationMessage, $police, $category, $details, $file_date));

        if (pg_affected_rows($notificationResult) > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Schedule details saved successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error inserting schedule into files table.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Report not found.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid update type.']);
}
?>
