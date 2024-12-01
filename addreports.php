<?php

session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
include "dbcon.php"; // Ensure this file establishes a PostgreSQL connection via pg_connect

$crime = $_POST['crime'];
$nameofvictim = $_POST['victim'];
$address = $_POST['address'];
$contact = $_POST['contact'];
$witness = implode(',', $_POST['witness']);
$witcontact = implode(',', $_POST['witcontact']);
$date = $_POST['datecrime'];
$time = $_POST['timecrime'];
$des = $_POST['description'];

$birthdate = $_POST['birthdate']; // Victim's birthdate
$gender = $_POST['gender']; // Male or Female
$email = $_POST['email']; // Victim's email
$username = $_POST['username']; // Victim's username
$password = $_POST['password']; // Victim's password

date_default_timezone_set('Asia/Manila');
$currentDate = new DateTime(); // Current date
$d = $currentDate->format('Y-m-d');

// Start a transaction for atomicity
pg_query($conn, "BEGIN");

try {
    // Insert victim details into residents table and get the resident_id
    $resident_sql = "INSERT INTO residents(
                        fullname, birthdate, barangay, phone, gender, email, username, password
                     ) 
                     VALUES ($1, $2, $3, $4, $5, $6, $7, $8)
                     RETURNING id"; // Return the inserted id

    // Prepare and execute the query
    $resident_result = pg_query_params($conn, $resident_sql, [
        $nameofvictim, $birthdate, $address, $contact, $gender, $email, $username, $password
    ]);

    if (!$resident_result) {
        throw new Exception("Error inserting into residents table: " . pg_last_error($conn));
    }

    // Fetch the resident_id of the recently inserted resident
    $resident_row = pg_fetch_assoc($resident_result);
    $resident_id = $resident_row['id'];

    // Insert report details into reports table
    $report_sql = "INSERT INTO reports(
                        category, name, address, contact, witness, witnessno, crimedate, time, description, file_date, finish, status, user_id, gender
                     ) 
                     VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14)"; // Add user_id

    // Prepare and execute the query
    $report_result = pg_query_params($conn, $report_sql, [
        $crime, $nameofvictim, $address, $contact, $witness, $witcontact,
        $date, $time, $des, $d, '', 'Acceptable', $resident_id, $gender
    ]);

    if (!$report_result) {
        throw new Exception("Error inserting into reports table: " . pg_last_error($conn));
    }

    // Commit the transaction
    pg_query($conn, "COMMIT");

    // Send email to the victim's email
    $mail = new PHPMailer(true);

    //Server settings
    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'st.peter.lifeplansinsurance@gmail.com';
                    $mail->Password   = 'scuh buyj yujs hdeo';                  // SMTP password
                    $mail->SMTPSecure = 'ssl';                                  // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                    $mail->Port       = 465; 

    //Recipients
    $mail->setFrom('st.peter.lifeplansinsurance@gmail.com');
    $mail->addAddress($email);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Guardian Watch Account Details';
    $mail->Body    = "Dear $nameofvictim, <br><br> 
                      Your account has been successfully created in the Guardian Watch Application. 
                      Below are your login details:<br><br>
                      <strong>Username:</strong> $username <br>
                      <strong>Password:</strong> $password <br><br>
                      Please keep these details safe. You can use these to access the application.<br><br>
                      Sincerely,<br>
                      Nasugbu Municipal Police Station";
    // Send the email
    if ($mail->send()) {
        echo 'Message has been sent to ' . $email;
    } else {
        echo 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
    }

    // Redirect or display success message
    $error_message = "You Successfully Created a report";
    $color = "p";   
    header("Location: adminreport.php?error_message=" . urlencode($error_message) . "&color=" . urlencode($color));

} catch (Exception $e) {
    // If any error occurs, roll back the transaction
    pg_query($conn, "ROLLBACK");
    echo "Error: " . $e->getMessage();
}

?>
