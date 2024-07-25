<?php
session_start();
include('dbcon.php'); // Make sure to include your database connection file

// Fetch the notification count from the emergency table
$sql = "SELECT COUNT(*) as notif_count FROM emergency";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$notif_count = $row['notif_count'];

// Fetch the notifications from the emergency table
$sql_notifications = "SELECT * FROM emergency ORDER BY id DESC";
$result_notifications = mysqli_query($conn, $sql_notifications);
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
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown" style="color:#ffffff">
                    <img src="upload/<?php
                        $id = $_SESSION['id'];
                        $role = $_SESSION['role'];
                        $sql = $role == 'admin' ? "SELECT image, fullname FROM admin WHERE id = '$id'" : "SELECT image, fullname FROM police WHERE id = '$id'";
                        $result = mysqli_query($conn, $sql);
                        $row = mysqli_fetch_assoc($result);
                        echo $row['image'];
                    ?>" alt="Profile" class="rounded-circle">
                    <span class="d-none d-md-block dropdown-toggle ps-2"><?php
                        echo $row['fullname'];
                    ?></span>
                </a><!-- End Profile Image Icon -->

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6><?php echo $row['fullname']; ?></h6>
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
            <a class="nav-link collapsed" href="adminmain.php" style="background-color:#add8e6;color: #184965;">
                <i class="bi bi-grid" style="color: #184965;"></i>
                <span>Dashboard</span>
            </a>
        </li><!-- End Dashboard Nav -->
        
        <li class="nav-item">
            <a class="nav-link collapsed" href="adminreport.php" style="background-color:#add8e6;color: #184965;">
                <i class="bi bi-person-lines-fill" style="color: #184965;"></i>
                <span>New Reports</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="adminpolice.php" style="background-color:#add8e6;color: #184965;">
                <i class="bi bi-people-fill" style="color: #184965;"></i>
                <span>Police</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link collapsed" href="adminoncase.php" style="background-color:#add8e6;color: #184965;">
                <i class="bi bi-card-list" style="color: #184965;"></i>
                <span>On-Going</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link collapsed" href="mapss.php" style="background-color:#add8e6;color: #184965;">
                <i class="bi bi-map" style="color: #184965;"></i>
                <span>Map</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link collapsed" href="reject.php" style="background-color:#add8e6;color: #184965;">
                <i class="bi bi-x-circle" style="color: #184965;"></i>
                <span>Reject Reports</span>
            </a>
        </li>

    </ul>

</aside><!-- End Sidebar -->

<!-- Notification Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <?php while ($row = mysqli_fetch_assoc($result_notifications)) { ?>
                        <li class="list-group-item">
                            <?php echo $row['location']; ?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->


</body>
</html>
