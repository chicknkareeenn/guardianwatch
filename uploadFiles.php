<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

include "dbcon.php"; // Database connection

// Receiving POST data
$id = $_POST['id'];
$user_id = $_POST['user_id'];
$name = $_POST['name'];
$category = $_POST['category'];
$description = $_POST['description'];
$file_date = $_POST['file_date'];
$policeId = $_POST['policeId'];
$notes = $_POST['notes'];

// Check for files and upload them
if (isset($_FILES['files'])) {
    $fileArray = $_FILES['files'];
    $uploadDir = 'uploads/'; // Upload directory on your server
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // Create directory if not exists
    }

    for ($i = 0; $i < count($fileArray['name']); $i++) {
        $fileName = basename($fileArray['name'][$i]);
        $fileTmpName = $fileArray['tmp_name'][$i];
        $fileError = $fileArray['error'][$i];

        if ($fileError === UPLOAD_ERR_OK) {
            $fileDest = $uploadDir . $fileName;
            if (move_uploaded_file($fileTmpName, $fileDest)) {
                // Upload file to GitHub
                $githubRepo = "chicknkareeenn/guardianwatch"; // Your GitHub repo
                $branch = "master"; // Branch to upload to
                $uploadUrl = "https://api.github.com/repos/$githubRepo/contents/uploads/$fileName"; // Correct path in GitHub

                // Read file content and encode in base64
                $content = base64_encode(file_get_contents($fileDest));

                // Prepare data for GitHub API
                $data = json_encode([
                    "message" => "Adding a new file to uploads folder",
                    "content" => $content,
                    "branch" => $branch
                ]);

                $githubToken = getenv('GITHUB_TOKEN');  // Use securely stored GitHub token

                if ($githubToken === false) {
                    die("Error: GitHub token not set.");
                }

                // GitHub request headers
                $headers = [
                    "Authorization: token $githubToken",
                    "Content-Type: application/json",
                    "User-Agent: GuardianWatchApp"
                ];

                // Initialize cURL request to upload file to GitHub
                $ch = curl_init($uploadUrl);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                // Execute the cURL request
                $response = curl_exec($ch);
                if (curl_errno($ch)) {
                    echo "cURL Error: " . curl_error($ch);
                    exit;
                }

                // Check if the upload was successful
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode == 201) {
                    // File uploaded to GitHub successfully, now insert into the DB
                    $insertQuery = "INSERT INTO files (reportid, user_id, fullname, category, details, file_date, filename, notes, police) 
                                    VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)";
                    $params = array($id, $user_id, $name, $category, $description, $file_date, $fileName, $notes, $policeId);

                    $result = pg_query_params($conn, $insertQuery, $params);

                    if ($result) {
                        echo "<script>alert('File uploaded and saved successfully.'); window.location.href='policereport.php';</script>";
                    } else {
                        echo "<script>alert('Error inserting file info into database: " . pg_last_error($conn) . "'); window.location.href='policereport.php';</script>";
                        exit();
                    }
                } else {
                    echo "<script>alert('Error uploading file to GitHub: $response'); window.location.href='policereport.php';</script>";
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
?>
