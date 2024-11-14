<?php
session_start();
include "dbcon.php"; // Including your PostgreSQL connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $announcement = $_POST['announcement'];
    $police_id = $_SESSION['id'];

    // Initialize array to store file paths
    $filePaths = [];

    if (isset($_FILES['announcement_files']) && !empty($_FILES['announcement_files']['name'][0])) {
        $fileArray = $_FILES['announcement_files'];
        $uploadDir = 'uploads/';

        // Ensure the upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Loop through the uploaded files
        for ($i = 0; $i < count($fileArray['name']); $i++) {
            $fileName = basename($fileArray['name'][$i]);
            $fileTmpName = $fileArray['tmp_name'][$i];
            $fileError = $fileArray['error'][$i];

            // Check for upload errors
            if ($fileError === UPLOAD_ERR_OK) {
                $fileDest = $uploadDir . $fileName;

                // Move the uploaded file to the desired directory
                if (move_uploaded_file($fileTmpName, $fileDest)) {
                    $filePaths[] = $fileDest;
                } else {
                    echo "Error moving file: $fileName";
                }
            } else {
                echo "Error uploading file: $fileName";
            }
        }
    }

    // Convert file paths to JSON format for storage
    $filePathsJson = json_encode($filePaths);

    // Use pg_query_params for PostgreSQL prepared statements
    $query = "INSERT INTO announcements (announcement, files, police_id) VALUES ($1, $2, $3)";
    $result = pg_query_params($conn, $query, array($announcement, $filePathsJson, $police_id));

    if ($result) {
        // Notify users about the new announcement
        $notifyUrl = 'http://192.168.1.13:8000/notifications';
        $notificationData = array(
            'message' => "A new announcement has been posted.",
            'user_id' => $police_id
        );

        $ch = curl_init($notifyUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notificationData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($notificationData))
        ));

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
        } else {
            $responseData = json_decode($response, true);
            echo 'Notification response: ' . $responseData['message'];
        }

        curl_close($ch);

        // Redirect with success message
        header("Location: announce.php?success_message=Announcement posted successfully");
    } else {
        // Redirect with error message
        header("Location: announce.php?error_message=Error posting announcement: " . pg_last_error($conn));
    }
}
?>
