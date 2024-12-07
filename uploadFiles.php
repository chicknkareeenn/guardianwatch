<?php
include "dbcon.php"; // PostgreSQL connection (pg_connect)

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

if (isset($_FILES['files'])) { // Change to single file 'file' instead of 'files'
    $file = $_FILES['files']; // Single file
    $uploadDir = 'upload';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileName = basename($file['name']);
    $fileTmpName = $file['tmp_name'];
    $fileError = $file['error'];

    if ($fileError === UPLOAD_ERR_OK) {
        $fileDest = $uploadDir . $fileName;
        if (move_uploaded_file($fileTmpName, $fileDest)) {
            // Upload file to GitHub after moving it locally
            $githubRepo = "chicknkareeenn/guardianwatch"; // Replace with your GitHub username/repo
            $branch = "master"; // Branch where you want to upload
            $uploadUrl = "https://api.github.com/repos/$githubRepo/contents/upload/$fileName";

            // Read the file content
            $content = base64_encode(file_get_contents($fileDest));

            // Prepare the request body
            $data = json_encode([
                "message" => "Adding a new file to upload folder",
                "content" => $content,
                "branch" => $branch
            ]);

            $githubToken = getenv('GITHUB_TOKEN');  // Access your GitHub token securely

            // Check if the token was retrieved successfully
            if ($githubToken === false) {
                die("Error: GitHub token is not set in the environment variables.");
            }

            // Prepare the headers
            $headers = [
                "Authorization: token $githubToken",
                "Content-Type: application/json",
                "User-Agent: GuardianWatchApp"
            ];

            // Initialize cURL
            $ch = curl_init($uploadUrl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Execute the request
            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                echo "cURL Error: " . curl_error($ch);
                exit();
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode == 201) {
                // File uploaded successfully to GitHub, proceed to insert into DB
                $insertQuery = "INSERT INTO files (reportid, user_id, fullname, category, details, file_date, filename, notes, police) 
                                VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)";
                $params = array($id, $user_id, $name, $category, $description, $file_date, $fileName, $notes, $policeId);

                $result = pg_query_params($conn, $insertQuery, $params);

                if (!$result) {
                    echo "<script>alert('Error inserting file info into database: " . pg_last_error($conn) . "'); window.location.href='policereport.php';</script>";
                    exit();
                }
            } else {
                echo "Error uploading file to GitHub: $response";
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
