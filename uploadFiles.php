<?php
session_start();

include "dbcon.php"; // Database connection

$id = $_POST['id'];
$user_id = $_POST['user_id'];
$name = $_POST['name'];
$category = $_POST['category'];
$description = $_POST['description'];
$file_date = $_POST['file_date'];
$policeId = $_POST['policeId'];
$notes = $_POST['notes'];

// Validate and upload the file
$file_name = $_FILES['files']['name'];
$file_size = $_FILES['files']['size'];
$tmp_name = $_FILES['files']['tmp_name'];
$error = $_FILES['files']['error'];

if ($error === 0) {
    if ($file_size > 10000000) { // 10 MB limit
        $error_message = "Sorry, your file is too large.";
        $color = "error";
        header("Location: policereport.php?error_message=" . urlencode($error_message) . "&color=" . $color);
        exit();
    }

    $file_ex = pathinfo($file_name, PATHINFO_EXTENSION);
    $file_ex_lc = strtolower($file_ex);
    $allowed_exs = ["pdf", "doc", "docx"];

    if (in_array($file_ex_lc, $allowed_exs)) {
        $new_file_name = time() . '_' . $file_name;
        $local_file_path = '/tmp/' . $new_file_name;

        if (!move_uploaded_file($tmp_name, $local_file_path)) {
            $error_message = "Failed to move uploaded file.";
            $color = "error";
            header("Location: policereport.php?error_message=" . urlencode($error_message) . "&color=" . $color);
            exit();
        }

        // Upload file to GitHub
        $githubRepo = "chicknkareeenn/guardianwatch"; // Your GitHub username/repo
        $branch = "master"; // Branch where you want to upload
        $uploadUrl = "https://api.github.com/repos/$githubRepo/contents/upload/$new_file_name";

        // Read the file content
        $content = base64_encode(file_get_contents($local_file_path));

        // Prepare the request body
        $data = json_encode([
            "message" => "Adding a new file to upload folder",
            "content" => $content,
            "branch" => $branch
        ]);

        $githubToken = getenv('GITHUB_TOKEN'); // GitHub token stored in environment variables

        if (!$githubToken) {
            $error_message = "Error: GitHub token is not set in the environment variables.";
            $color = "error";
            header("Location: policereport.php?error_message=" . urlencode($error_message) . "&color=" . $color);
            exit();
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
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Handle cURL errors
        if ($response === false) {
            $error_message = "cURL error: " . curl_error($ch);
            $color = "error";
            header("Location: policereport.php?error_message=" . urlencode($error_message) . "&color=" . $color);
            exit();
        } else {
            $responseData = json_decode($response, true);
            if ($httpCode == 201) {
                // File uploaded successfully to GitHub, proceed to insert into DB
                $sql = "INSERT INTO files (reportid, user_id, fullname, category, details, file_date, filename, notes, police) 
                        VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)";
                
                $stmt = pg_prepare($conn, "insert_files", $sql);
                $result = pg_execute($conn, "insert_files", array($id, $user_id, $name, $category, $description, $file_date, $new_file_name, $notes, $policeId));

                if ($result) {
                    // Update the report status to 'Ongoing'
                    $updateQuery = "UPDATE reports SET finish = 'Ongoing' WHERE id = $1";
                    $updateResult = pg_query_params($conn, $updateQuery, array($id));

                    if (!$updateResult) {
                        $error_message = "Error updating report status: " . pg_last_error($conn);
                        $color = "error";
                        header("Location: policereport.php?error_message=" . urlencode($error_message) . "&color=" . $color);
                        exit();
                    }

                    $error_message = "Report is now Ongoing.";
                    $color = "success";
                    header("Location: policereport.php?error_message=" . urlencode($error_message) . "&color=" . $color);
                    exit();
                } else {
                    $error_message = "Error: Could not execute the query.";
                    $color = "error";
                    header("Location: policereport.php?error_message=" . urlencode($error_message) . "&color=" . $color);
                    exit();
                }
            } else {
                $error_message = "Error uploading file to GitHub: $response";
                $color = "error";
                header("Location: policereport.php?error_message=" . urlencode($error_message) . "&color=" . $color);
                exit();
            }
        }
        
        curl_close($ch);
    } else {
        $error_message = "Only PDF, DOC, and DOCX files are allowed.";
        $color = "error";
        header("Location: policereport.php?error_message=" . urlencode($error_message) . "&color=" . $color);
        exit();
    }
} else {
    $error_message = "An unknown error occurred during file upload.";
    $color = "error";
    header("Location: policereport.php?error_message=" . urlencode($error_message) . "&color=" . $color);
    exit();
}
?>
