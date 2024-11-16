<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

include "dbcon.php"; // Database connection

$username = $_POST['username'];
$password = $_POST['password'];
$fullname = $_POST['fullname'];
$rank = $_POST['ran'];
$contact = $_POST['contact'];
$category = $_POST['category'];
$email = $_POST['email'];
$role = "Police";

// Generate a unique token for email verification
$verification_token = bin2hex(random_bytes(16));

// Validate and upload the image
$img_name = $_FILES['image']['name'];
$img_size = $_FILES['image']['size'];
$tmp_name = $_FILES['image']['tmp_name'];
$error = $_FILES['image']['error'];

if ($error === 0) {
    if ($img_size > 10000000000) {
        echo "Sorry, your file is too large.";
        exit;
    } else {
        $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
        $img_ex_lc = strtolower($img_ex);
        $allowed_exs = array("png", "jpg");

        if (in_array($img_ex_lc, $allowed_exs)) {
            $new_img_name = time() . '_' . $img_name;
            $local_file_path = '/tmp/' . $new_img_name;
            move_uploaded_file($tmp_name, $local_file_path);

            // Upload file to GitHub
            $githubRepo = "chicknkareeenn/guardianwatch"; // Replace with your GitHub username/repo
            $branch = "master"; // Branch where you want to upload
            $uploadUrl = "https://api.github.com/repos/$githubRepo/contents/upload/$new_img_name";

            // Read the file content
            $content = base64_encode(file_get_contents($local_file_path));

            // Prepare the request body
            $data = json_encode([
                "message" => "Adding a new file to upload folder",
                "content" => $content,
                "branch" => $branch
            ]);

            // Prepare the headers
            $headers = [
                "Authorization: token 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",
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
                exit;
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode == 201) {
                // File uploaded successfully to GitHub, proceed to insert into DB
                $sql = "INSERT INTO police (username, password, role, fullname, ran, image, contact, status, assign, email, verification_token)
                        VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11)";

                $stmt = pg_prepare($conn, "insert_police", $sql);
                $result = pg_execute($conn, "insert_police", array(
                    $username, $password, $role, $fullname, $rank, $new_img_name, $contact, 'Available', $category, $email, $verification_token
                ));

                if ($result) {
                    // Send confirmation email
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'st.peter.lifeplansinsurance@gmail.com';
                        $mail->Password   = 'scuh buyj yujs hdeo';
                        $mail->SMTPSecure = 'ssl';
                        $mail->Port       = 465;

                        $mail->setFrom('st.peter.lifeplansinsurance@gmail.com');
                        $mail->addAddress($email);

                        $mail->isHTML(true);
                        $mail->Subject = 'Email Verification';
                        $mail->Body    = 'Hello ' . $fullname . ',<br><br>'
                                         . 'Please confirm your email address by clicking the link below:<br><br>'
                                         . '<a href="https://guardianwatch.onrender.com/confirm_email.php?token=' . $verification_token . '">Confirm Email</a><br><br>'
                                         . 'Best Regards,<br>Guardian Watch';

                        $mail->send();

                        $error_message = "You successfully created a new Police account. Please check your email to verify your address.";
                        $color = "p";
                        header("Location: adminpolice.php?error_message=" . $error_message . "&color=" . $color);
                    } catch (Exception $e) {
                        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    }
                } else {
                    echo "Error: Could not execute the query.";
                }
            } else {
                echo "Error uploading file to GitHub: $response";
            }
        } else {
            echo "Only PNG and JPG images are allowed.";
        }
    }
} else {
    echo "An unknown error occurred!";
}
?>
