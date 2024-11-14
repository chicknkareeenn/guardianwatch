<?php
include "dbcon.php"; // PostgreSQL connection (pg_connect)

set_time_limit(10); // Set a maximum execution time to avoid indefinite loading

$id = $_POST['id'];
$user_id = $_POST['user_id'];
$name = $_POST['name'];
$category = $_POST['category'];
$description = $_POST['description'];
$file_date = $_POST['file_date'];
$policeId = $_POST['policeId'];
$notes = $_POST['notes'];

// Check if ID is provided
if (empty($id)) {
    echo "<script>alert('Error: Report ID is missing.'); window.location.href='policereport.php';</script>";
    exit();
}

if (isset($_FILES['files'])) {
    $fileArray = $_FILES['files'];
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    for ($i = 0; $i < count($fileArray['name']); $i++) {
        $fileName = basename($fileArray['name'][$i]);
        $fileTmpName = $fileArray['tmp_name'][$i];
        $fileError = $fileArray['error'][$i];

        if ($fileError === UPLOAD_ERR_OK) {
            $fileDest = $uploadDir . $fileName;
            if (move_uploaded_file($fileTmpName, $fileDest)) {
                $insertQuery = "INSERT INTO files (reportid, user_id, fullname, category, details, file_date, filename, notes, police) 
                                VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)";
                $params = array($id, $user_id, $name, $category, $description, $file_date, $fileName, $notes, $policeId);

                $result = pg_query_params($conn, $insertQuery, $params);

                if (!$result) {
                    echo "<script>alert('Error inserting file info into database: " . pg_last_error($conn) . "'); window.location.href='policereport.php';</script>";
                    exit();
                }
            } else {
                echo "<script>alert('Error moving file: $fileName'); window.location.href='policereport.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Error uploading file: $fileName'); window.location.href='policereport.php';</script>";
            exit();
        }
    }
}

// Update the report status
$updateQuery = "UPDATE reports SET finish = 'Ongoing' WHERE id = $1";
$updateResult = pg_query_params($conn, $updateQuery, array($id));

if ($updateResult) {
    echo "<script>alert('File uploaded, info saved, and report status updated to Ongoing successfully.'); window.location.href='policereport.php';</script>";
} else {
    echo "<script>alert('Error updating report status: " . pg_last_error($conn) . "'); window.location.href='policereport.php';</script>";
    exit();
}
?>
