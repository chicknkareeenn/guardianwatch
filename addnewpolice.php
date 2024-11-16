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
$verification_token = bin2hex(random_bytes(16)); // Generate a secure token

// Get upload path from environment variable or fallback to default path
$upload_dir = getenv('UPLOAD_PATH') ?: __DIR__ . '/uploads'; // Fallback to /uploads if not set in environment

// Debugging: Check the upload directory path
echo "Upload directory: $upload_dir<br>";

if (!is_dir($upload_dir)) {
    // Try creating the directory and ensure it is writable
    echo "Directory does not exist. Attempting to create: $upload_dir<br>";
    if (mkdir($upload_dir, 0777, true)) {
        echo "Directory created successfully.<br>";
    } else {
        echo "Failed to create directory or insufficient permissions.<br>";
    }
} else {
    echo "Directory exists: $upload_dir<br>";
}

// Ensure the directory is writable
if (is_writable($upload_dir)) {
    echo "Directory is writable.<br>";
} else {
    echo "Directory is not writable.<br>";
}

$img_name = $_FILES['image']['name'];
$img_size = $_FILES['image']['size'];
$tmp_name = $_FILES['image']['tmp_name'];
$error = $_FILES['image']['error'];

if ($error === 0) {
    // File size limit (5MB in this case)
    if ($img_size > 5000000) {
        echo "Sorry, your file is too large.";
    } else {
        $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
        $img_ex_lc = strtolower($img_ex);
        $allowed_exs = array("png", "jpg");

        // Check if the file has an allowed extension
        if (in_array($img_ex_lc, $allowed_exs)) {
            $new_img_name = time() . '_' . $img_name; // Make the image name unique
            $img_upload_path = $upload_dir . '/' . $new_img_name; // Use dynamic upload path

            // Debugging: Check if the file is moved correctly
            echo "Attempting to move file to: $img_upload_path<br>";

            if (move_uploaded_file($tmp_name, $img_upload_path)) {
                echo "File uploaded successfully.<br>";

                // PostgreSQL query using prepared statements to insert data
                $sql = "INSERT INTO police (username, password, role, fullname, ran, image, contact, status, assign, email, verification_token)
                        VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11)";

                // Prepare statement
                $stmt = pg_prepare($conn, "insert_police", $sql);

                // Execute the statement
                $result = pg_execute($conn, "insert_police", array(
                    $username, $password, $role, $fullname, $rank, $new_img_name, $contact, 'Available', $category, $email, $verification_token
                ));

                if ($result) {
                    // Send confirmation email
                    $mail = new PHPMailer(true);
                    try {
                        // Server settings
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'st.peter.lifeplansinsurance@gmail.com';
                        $mail->Password   = 'scuh buyj yujs hdeo'; // SMTP password
                        $mail->SMTPSecure = 'ssl';  // Enable SSL encryption
                        $mail->Port       = 465;

                        // Recipients
                        $mail->setFrom('st.peter.lifeplansinsurance@gmail.com');
                        $mail->addAddress($email);

                        // Content
                        $mail->isHTML(true);
                        $mail->Subject = 'Email Verification';
                        $mail->Body    = 'Hello ' . $fullname . ',<br><br>'
                                         . 'Please confirm your email address by clicking the link below:<br><br>'
                                         . '<a href="https://guardianwatch.onrender.com/confirm_email.php?token=' . $verification_token . '">Confirm Email</a><br><br>'
                                         . 'Best Regards,<br>Guardian Watch';

                        $mail->send();

                        // Redirect or show success message
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
                echo "File upload failed.<br>";
            }
        } else {
            echo "Only PNG and JPG images are allowed.";
        }
    }
} else {
    echo "An unknown error occurred!";
}
?>
