<?php
include "dbcon.php";

$id = $_POST['id'];
$user_id = $_POST['user_id'];
$name = $_POST['name'];
$category = $_POST['category'];
$description = $_POST['description'];
$file_date = $_POST['file_date'];
$policeId = $_POST['policeId'];
$notes = $_POST['notes'];

if (isset($_FILES['files'])) {
    $fileArray = $_FILES['files'];

    // Ensure the files directory exists
    $uploadDir = 'uploads/';
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
                // Insert file info into database
                $stmt = $conn->prepare("INSERT INTO files (reportId, user_id, fullname, category, details, file_date, filename, notes, police) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("iissssssi", $id, $user_id, $name, $category, $description, $file_date, $fileName, $notes, $policeId);
                if ($stmt->execute()) {
                    echo "File uploaded and info saved successfully: $fileName";

                    // Notify users about the new file upload
                    $notifyUrl = 'http://192.168.1.13:8000/notifications';
                    $notificationData = array(
                        'message' => "A new file has been uploaded: $fileName",
                        'user_id' => $user_id
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
                    if(curl_errno($ch)) {
                        echo 'cURL error: ' . curl_error($ch);
                    } else {
                        $responseData = json_decode($response, true);
                        echo 'Notification response: ' . $responseData['message'];
                    }

                    curl_close($ch);
                } else {
                    echo "Error inserting file info into database: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Error moving file: $fileName";
            }
        } else {
            echo "Error uploading file: $fileName";
        }
    }
}

// Update the reports table
$updateStmt = $conn->prepare("UPDATE reports SET finish = 'Ongoing' WHERE id = ?");
$updateStmt->bind_param("i", $id);
if ($updateStmt->execute()) {
    echo "Report status updated to 'Ongoing'.";
} else {
    echo "Error updating report status: " . $updateStmt->error;
}
$updateStmt->close();

// Redirect or output success message
header("Location: policereport.php"); // Replace with your success page or redirection URL
exit();
?>
