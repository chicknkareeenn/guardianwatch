<?php
session_start();
include "dbcon.php";  // Make sure this doesn't output anything before headers

$email = $_POST['username'];
$pass = $_POST['password'];

// Prepare the SQL statement with placeholders
$sql = "SELECT * FROM (
            SELECT id, role, username, password FROM admin 
            UNION 
            SELECT id, role, username, password FROM police 
            UNION 
            SELECT id, role, username, password FROM residents 
        ) combined_table
        WHERE username = $1 AND password = $2";

// Prepare and execute the SQL statement
$result = pg_query_params($conn, $sql, array($email, $pass));

if ($result) {
    $row = pg_fetch_assoc($result);

    // Check if login is successful
    if ($row && pg_num_rows($result) === 1) {
        $_SESSION['role'] = $row['role'];
        $_SESSION['id'] = $row['id'];

        // Redirect before any output
        header('refresh:3; url=session.php');
        // Exit to avoid further output
        exit();
    } else {
        // Handle failed login
        echo "Invalid credentials.";
    }
} else {
    // Handle failed query execution
    echo "Failed to execute query.";
}

// Show loading screen AFTER the redirect headers
echo "
<style>
    body {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        height: 100vh;
        margin: 0;
        background-color: #f0f8ff;
    }

    .loader-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .loader {
        border: 10px solid #f3f3f3;
        border-top: 10px solid #184965;
        border-radius: 50%;
        width: 100px;
        height: 100px;
        animation: spin 1s linear infinite;
        margin-bottom: 20px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .loading-text {
        font-family: Arial, sans-serif;
        font-weight: bold;
        font-size: 24px;
        color: #184965;
        text-align: center;
        margin-top: 10px;
        animation: fadeIn 1s ease-in-out infinite alternate;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
<body>
    <div class='loader-container'>
        <div class='loader'></div>
        <div class='loading-text'>Logging you in...</div>
    </div>
</body>";
?>
