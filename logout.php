<?php
	session_start();
	session_destroy();


	$success =" <style>
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      height: 100vh;
      margin: 0;
    }

    .loader-container {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .loader {
      border: 10px solid #f3f3f3;
      border-top: 8px solid #da649c;
      border-radius: 50%;
      width: 150px;
      height: 150px;
      animation: spin 1s linear infinite;
      margin-bottom: 10px;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .loading-text {
      font: arial;
      font-weight: bold;
      font-size: 30px;
      color:  #da649c;
      text-align: center;
    }
  </style>
<body>
  <div class='loader-container'>
    <div class='loader'></div>
    <div class='loading-text'>Logout..</div>
  </div>
</body>";
	header('refresh:3; url=index.php');
	echo $success;
        	



?>