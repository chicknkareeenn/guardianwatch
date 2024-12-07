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
} else if ($type === 'schedule') {
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
}else if ($type === 'closure') {
    // Closure update logic
    $closureSummary = $data['closureSummary'];
    $closureReason = $data['closureReason'];

    // Handle file upload
    $file_name = $_FILES['courtDecisionLetter']['name'];
    $file_size = $_FILES['courtDecisionLetter']['size'];
    $tmp_name = $_FILES['courtDecisionLetter']['tmp_name'];
    $error = $_FILES['courtDecisionLetter']['error'];
    
    if ($error === 0) {
        if ($file_size > 10000000) { // 10 MB limit
            $error_message = "Sorry, your file is too large.";
            $color = "error";
            header("Location: oncase.php?error_message=" . urlencode($error_message) . "&color=" . $color);
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
                header("Location: oncase.php?error_message=" . urlencode($error_message) . "&color=" . $color);
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
                header("Location: oncase.php?error_message=" . urlencode($error_message) . "&color=" . $color);
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
                header("Location: oncase.php?error_message=" . urlencode($error_message) . "&color=" . $color);
                exit();
            } else {
                $responseData = json_decode($response, true);
                if ($httpCode != 201) {
                    echo json_encode(['status' => 'error', 'message' => 'Error uploading file to GitHub: ' . $responseData['message']]);
                    exit();
                }
                curl_close($ch);
    
                // After successful upload to GitHub, proceed with database operations
    
                // Fetch the necessary report data from the reports table
                $query = "SELECT user_id, name, category, description, police_assign, file_date FROM reports WHERE id = $1";
                $result = pg_prepare($conn, "fetch_report_data_closure", $query);
                $result = pg_execute($conn, "fetch_report_data_closure", array($reportId));
    
                if ($row = pg_fetch_assoc($result)) {
                    $userId = $row['user_id'];
                    $fullname = $row['name'];
                    $category = $row['category'];
                    $details = $row['description'];
                    $police = $row['police_assign'];
                    $file_date = $row['file_date'];
    
                    // Update the finish column to 'Closed' in the reports table
                    $updateQuery = "UPDATE reports SET finish = 'Closed' WHERE id = $1";
                    $updateStmt = pg_prepare($conn, "update_report_finish", $updateQuery);
                    $updateResult = pg_execute($conn, "update_report_finish", array($reportId));
    
                    if (pg_affected_rows($updateResult) > 0) {
                        // Insert into files table with closure details and uploaded document filename
                        $notificationMessage = "Closure summary: $closureSummary. Reasons for resolution: $closureReason.";
                        $notificationQuery = "
                            INSERT INTO files (reportid, user_id, fullname, notes, police, category, details, file_date, filename, time)
                            VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, NOW())
                        ";
                        $notificationStmt = pg_prepare($conn, "insert_closure_file", $notificationQuery);
                        $notificationResult = pg_execute($conn, "insert_closure_file", array(
                            $reportId, $userId, $fullname, $notificationMessage, $police, $category, $details, $file_date, $new_file_name
                        ));
    
                        if (pg_affected_rows($notificationResult) > 0) {
                            echo json_encode(['status' => 'success', 'message' => 'Closure details saved, notification sent, and document uploaded.']);
                        } else {
                            echo json_encode(['status' => 'error', 'message' => 'Error inserting closure into files table.']);
                        }
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Error updating report closure status.']);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Report not found.']);
                }
            }
        } else {
            // No file uploaded, set the filename to null
            $new_file_name = null;
        }

    }
}else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid update type.']);
}
?>
