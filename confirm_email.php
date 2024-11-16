<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        .container h1 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 20px;
        }

        .container p {
            font-size: 1rem;
            color: #666;
            margin-bottom: 20px;
        }

        .container button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .container button:hover {
            background-color: #0056b3;
        }

        .error {
            color: #d9534f;
            font-size: 1rem;
        }
    </style>
</head>
<body>
<?php
include "dbcon.php"; // Database connection

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists in the database
    $sql = "SELECT * FROM police WHERE verification_token = $1 and email_verified = FALSE";
    $stmt = pg_prepare($conn, "select_token", $sql);
    $result = pg_execute($conn, "select_token", array($token));

    if (pg_num_rows($result) > 0) {
        $user = pg_fetch_assoc($result);
        $email = $user['email'];

        // Show the confirmation page
        echo '<div class="container">
                <h1>Email Verification for Guardian Watch</h1>
                <p>We have found your email: <strong>' . htmlspecialchars($email) . '</strong></p>
                <p>By clicking "Confirm", you agree to receive your login credentials.</p>
                <form id="confirmForm" method="GET" action="verify_email.php">
                    <input type="hidden" name="token" value="' . htmlspecialchars($token) . '">
                    <button type="button" onclick="confirmAction()">Confirm</button>
                </form>
              </div>';
    } else {
        echo '<div class="container">
                <h1 class="error">Verification Failed</h1>
                <p class="error">Invalid or expired token.</p>
              </div>';
    }
} else {
    echo '<div class="container">
            <h1 class="error">No Token Provided</h1>
            <p class="error">Please check your email for the verification link.</p>
          </div>';
}
?>

<script type="text/javascript">
    // Function to trigger the confirmation dialog
    function confirmAction() {
        // Show the confirmation dialog
        var confirmation = confirm("Are you sure you want to confirm and receive your login credentials?");
        
        // If the user confirms, submit the form
        if (confirmation) {
            document.getElementById("confirmForm").submit();
        }
    }
</script>

</body>
</html>
