<?php 
session_start();
include "dbcon.php";
if (!isset($_SESSION['role']) || (trim($_SESSION['role']) == '')) {
    header('location:main.php');
    exit();
}

$policeAssign = isset($_SESSION['id']) ? $_SESSION['id'] : '';

// Fetch the notification count from the notifications table
// Fetch the notification count from the emergency table
// Fetch the notification count from the notifications table
// Query to count notifications
$sql = "SELECT COUNT(*) AS notif_count FROM notifications WHERE police_id = '$policeAssign'";
$result = pg_query($conn, $sql);
$row = pg_fetch_assoc($result);
$notif_count = $row['notif_count'];

// Query to fetch notifications and corresponding user data from residents table
$sql_notifications = "SELECT notifications.*, residents.fullname, residents.id
                      FROM notifications
                      JOIN residents ON notifications.user_id = residents.id
                      WHERE police_id = '$policeAssign'
                      ORDER BY notifications.notif_id DESC";
$result_notifications = pg_query($conn, $sql_notifications);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags and CSS links -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="path/to/bootstrap.css"> <!-- Include Bootstrap CSS -->
    <script src="path/to/bootstrap.bundle.js"></script> <!-- Include Bootstrap JS bundle -->
</head>
<body>
<header id="header" class="header fixed-top d-flex align-items-center" style="background-color: #184965;">
    <div class="d-flex align-items-center justify-content-between" style="background-color: #184965;">
        <a href="adminmain.php" class="logo d-flex align-items-center">
            <img src="crimelogo.png" alt="" style="border-radius: 50%;">
            <span class="d-none d-lg-block" style="color: #ffffff;">GuardianWatch</span>
        </a>
        <i class="bi bi-list toggle-sidebar-btn" style="color: white;"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <li class="nav-item">
                <a class="nav-link nav-icon" href="#" data-bs-toggle="modal" data-bs-target="#notificationModal">
                    <i class="bi bi-bell" style="color: white;"></i>
                    <!-- Badge with notification count -->
                    <span class="badge bg-danger badge-number"><?php echo $notif_count; ?></span>
                </a><!-- End Notification Icon -->
            </li>

            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown" style = "color: #ffffff">
                    <img src="upload/<?php
                        $id = $_SESSION['id'];
                        $role = $_SESSION['role'];
                        $sql = $role == 'admin' ? "SELECT image, fullname FROM admin WHERE id = '$id'" : "SELECT image, fullname FROM police WHERE id = '$id'";
                        $result = pg_query($conn, $sql);
                        $row = pg_fetch_assoc($result);
                        echo $row['image'];
                    ?>" alt="Profile" class="rounded-circle">
                    <span class="d-none d-md-block dropdown-toggle ps-2"><?php
                        echo $row['fullname'];
                    ?></span>
                </a><!-- End Profile Image Icon -->

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6><?php
                            echo $row['fullname'];
                        ?></h6>
                        <span><?php echo ucfirst($role); ?></span>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul><!-- End Profile Dropdown Items -->
            </li><!-- End Profile Nav -->

        </ul>
    </nav><!-- End Icons Navigation -->

</header><!-- End Header -->

<aside id="sidebar" class="sidebar" style="background-color:#add8e6;color: #184965;">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link collapsed" href="policemain.php" style="background-color:#add8e6;color: #184965;">
                <i class="bi bi-grid" style="color: #184965 ;"></i>
                <span>Dashboard</span>
            </a>
        </li><!-- End Dashboard Nav -->
        
        <li class="nav-item">
            <a class="nav-link collapsed" href="policereport.php" style="background-color:#add8e6;color: #184965;">
                <i class="bi bi-person-lines-fill" style="color: #184965;"></i>
                <span>New Reports</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link collapsed" href="oncase.php" style="background-color:#add8e6;color: #184965;">
                <i class="bi bi-card-list" style="color: #184965;"></i>
                <span>On-Going Reports</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="closed.php" style="background-color:#add8e6;color: #184965;">
                <i class="bi bi-card-list" style="color: #184965;"></i>
                <span>Solved Reports</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link collapsed" href="mapsPolice.php" style="background-color:#add8e6;color: #184965;">
                <i class="bi bi-map" style="color: #184965;"></i>
                <span>Emergency Map</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="announce.php" style="background-color:#add8e6;color: #184965;">
                <i class="bi-megaphone" style="color: #184965;"></i>
                <span>Announcements</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link collapsed" href="documents.php" style="background-color:#add8e6;color: #184965;">
                <i class="bi-file-earmark" style="color: #184965;"></i>
                <span>Documents</span>
            </a>
        </li>

    </ul>

</aside><!-- End Sidebar -->

<!-- Notification Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Use modal-lg for larger modals -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Display notifications in a table -->
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Notification</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = pg_fetch_assoc($result_notifications)) { ?>
                            <tr>
                                <td><?php echo $row['fullname']; ?></td>
                                <td><?php echo $row['notif']; ?></td>
                                <td><?php echo $row['chat_date']; ?></td>
                                <td>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="handleReply(<?php echo $row['notif_id']; ?>)">
                                    <i class="bi bi-reply"></i>
                                </button>

                                 
                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                    onclick="handleRemove(this, <?php echo $row['id']; ?>)" data-id="<?php echo $row['notif_id']; ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                            </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="conversationModal" tabindex="-1" aria-labelledby="conversationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="conversationModalLabel">Conversation with <span id="userFullName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Conversation chat area -->
                <div id="chatArea" style="height: 400px; overflow-y: auto; padding: 10px; background-color: #f8f9fa;">
                    <!-- Chat messages will be dynamically loaded here -->
                </div>

                <!-- Reply input -->
                <div class="input-group mt-3">
                    <input type="text" id="replyMessage" class="form-control" placeholder="Type your message">
                    <button class="btn btn-primary" type="button" onclick="sendReply()">Send</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentNotificationId = null;

function handleReply(notificationId) {
    currentNotificationId = notificationId; // Store the notification ID
    console.log("Replying to notification ID:", currentNotificationId); // For debugging

    // Fetch chat history for the given notification ID
    fetchChatHistory(currentNotificationId);
}

function fetchChatHistory(notificationId) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'fetch_chat_history.php?notification_id=' + notificationId, true);
    xhr.onload = function () {
        if (this.status === 200) {
            const response = JSON.parse(this.responseText);
            console.log(response); // Log the response for debugging
            const chatArea = document.getElementById('chatArea');
            chatArea.innerHTML = '';

            // Display notification message
            const notificationMessage = document.createElement('div');
            notificationMessage.style.marginBottom = '10px';
            notificationMessage.style.padding = '10px';
            notificationMessage.style.backgroundColor = '#f1f1f1';
            notificationMessage.style.borderRadius = '10px';
            notificationMessage.innerHTML = `<strong>Notification:</strong> ${response.notif}`;
            chatArea.appendChild(notificationMessage);

            // Display user's full name
            document.getElementById('userFullName').innerText = response.fullname;

            // Load chat messages
            response.messages.forEach(msg => {
                const messageBubble = document.createElement('div');
                messageBubble.style.marginBottom = '10px';
                messageBubble.style.padding = '5px 8px';
                messageBubble.style.borderRadius = '10px';

                if (msg.sender_role === 'user') {
                    messageBubble.style.backgroundColor = '#e2e3e5';
                    messageBubble.style.textAlign = 'left';
                    messageBubble.innerHTML = `<strong>${response.fullname}:</strong> ${msg.message}`;
                } else {
                    messageBubble.style.backgroundColor = '#0d6efd';
                    messageBubble.style.color = 'white';
                    messageBubble.style.textAlign = 'right';
                    messageBubble.innerHTML = `<strong>Police:</strong> ${msg.message}`;
                }

                chatArea.appendChild(messageBubble);
            });

            chatArea.scrollTop = chatArea.scrollHeight;

            const conversationModal = new bootstrap.Modal(document.getElementById('conversationModal'));
            conversationModal.show();
        } else {
            console.error('Error fetching chat history:', this.status, this.statusText);
        }
    };
    xhr.send();
}

function sendReply() {
    const replyMessage = document.getElementById('replyMessage').value;
    if (replyMessage.trim() === '') {
        alert('Please enter a message.');
        return;
    }

    // Send reply via AJAX
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'send_reply.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (this.status === 200) {
            const response = JSON.parse(this.responseText);
            if (response.success) {
                // Clear the input field and reload the conversation
                document.getElementById('replyMessage').value = '';
                fetchChatHistory(currentNotificationId); // Reload chat history
            } else {
                alert('Failed to send reply: ' + response.error);
            }
        }
    };
    
    // Include the notification ID in the POST request
    xhr.send(`notification_id=${currentNotificationId}&reply_message=${encodeURIComponent(replyMessage)}`);
}

</script>

</body>
</html>