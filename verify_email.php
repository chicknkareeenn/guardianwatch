<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
include "dbcon.php"; 

// Check if a token is provided in the URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists in the database
    $sql = "SELECT * FROM police WHERE verification_token = $1";
    $stmt = pg_prepare($conn, "select_token", $sql);
    $result = pg_execute($conn, "select_token", array($token));

    if (pg_num_rows($result) > 0) {
        // Token is valid, update email_verified to TRUE
        $sql_update = "UPDATE police SET email_verified = TRUE WHERE verification_token = $1";
        $stmt_update = pg_prepare($conn, "update_email_verified", $sql_update);
        $result_update = pg_execute($conn, "update_email_verified", array($token));

        if ($result_update) {
            // Fetch user details (username and password)
            $sql_select = "SELECT username, password, fullname, email FROM police WHERE verification_token = $1";
            $stmt_select = pg_prepare($conn, "select_user_details", $sql_select);
            $result_select = pg_execute($conn, "select_user_details", array($token));

            if (pg_num_rows($result_select) > 0) {
                $user = pg_fetch_assoc($result_select);
                $username = $user['username'];
                $password = $user['password'];
                $fullname = $user['fullname'];
                $email = $user['email'];

                // Send email with the login credentials
                $mail = new PHPMailer(true);
                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server to send through
                    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                    $mail->Username   = 'st.peter.lifeplansinsurance@gmail.com';               // SMTP username
                    $mail->Password   = 'scuh buyj yujs hdeo';                  // SMTP password
                    $mail->SMTPSecure = 'ssl';                                  // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                    $mail->Port       = 465;

                    // Recipients
                    $mail->setFrom('st.peter.lifeplansinsurance@gmail.com');
                    $mail->addAddress($email);   // Add the police officer's email address

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Guardian Watch: Your Account Credentials';
                    $mail->Body    = 'Hello ' . $fullname . ',<br><br>' 
                                     . 'Your email has been successfully verified. Below are your login credentials for Guardian Watch:<br><br>'
                                     . '<strong>Username:</strong> ' . $username . '<br>'
                                     . '<strong>Password:</strong> ' . $password . '<br><br>'
                                     . 'Please keep this information secure.<br><br>'
                                     . 'Best regards,<br>Guardian Watch System';

                    // Send the email
                    $mail->send();

                    // Show success alert and redirect to another page (or the same page if needed)
                    echo '<script type="text/javascript">
                            alert("Email verified successfully! Your credentials have been sent to your email.");
                            window.location.href = "index.php";  // Redirect to a page after success
                          </script>';
                } catch (Exception $e) {
                    echo '<script type="text/javascript">
                            alert("Message could not be sent. Mailer Error: ' . $mail->ErrorInfo . '");
                          </script>';
                }
            } else {
                echo '<script type="text/javascript">
                        alert("Error fetching user details.");
                      </script>';
            }
        } else {
            echo '<script type="text/javascript">
                    alert("Error verifying the email.");
                  </script>';
        }
    } else {
        echo '<script type="text/javascript">
                alert("Invalid or expired token.");
              </script>';
    }
} else {
    echo '<script type="text/javascript">
            alert("No token provided.");
          </script>';
}
?>
