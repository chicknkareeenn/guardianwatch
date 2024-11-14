<?php
session_start();
session_destroy();

$success = "
<style>
    body {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        height: 100vh;
        margin: 0;
        background-color: #f0f8ff; /* Light background for contrast */
    }

    .loader-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .loader {
        border: 10px solid #f3f3f3;
        border-top: 10px solid #184965; /* Changed color */
        border-radius: 50%;
        width: 100px; /* Reduced size for a more compact look */
        height: 100px; /* Reduced size for a more compact look */
        animation: spin 1s linear infinite;
        margin-bottom: 20px; /* Increased margin for better spacing */
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .loading-text {
        font-family: Arial, sans-serif; /* Added a fallback font */
        font-weight: bold;
        font-size: 24px; /* Adjusted size for better readability */
        color: #184965; /* Changed color */
        text-align: center;
        margin-top: 10px; /* Added margin for spacing */
        animation: fadeIn 1s ease-in-out infinite alternate; /* Added fading effect */
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
<body>
    <div class='loader-container'>
        <div class='loader'></div>
        <div class='loading-text'>Logging you out...</div>
    </div>
</body>";

header('refresh:3; url=index.php');
echo $success;
?>
