<?php 
session_start();
include "dbcon.php";

if (!isset($_SESSION['role']) || (trim($_SESSION['role']) == '')) {
    header('location:index.php');
    exit();
}  

$reportId = $_GET['id'];

// Fetch report details from the database (PostgreSQL syntax)
$sql = "SELECT 
            r.id, 
            r.category,  
            r.name,
            r.valid_id, 
            r.address, 
            r.contact, 
            r.witness, 
            r.witnessno, 
            r.crimedate, 
            r.time, 
            r.description, 
            r.injury,  
            r.location, 
            r.evidence, 
            r.file_date,  
            r.finish, 
            p.fullname as police,
            u.fullname AS userfull,
            r.user_id
        FROM reports AS r
        LEFT JOIN police AS p ON p.id = r.police_assign
        LEFT JOIN residents AS u ON r.user_id = u.id
        WHERE r.id = '$reportId'";

$result = pg_query($conn, $sql);
$report = pg_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Crime Reports</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="logooo.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600|Poppins:300,400,500,600,700" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f7f8;
            font-family: 'Open Sans', sans-serif;
        }
        .header {
            background-color: #57737A;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .card {
            border-radius: 10px;
            border: none;
            background: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }
        .form-label {
            color: #57737A;
            font-weight: 600;
        }
        .form-control {
            border: 1px solid #57737A;
            border-radius: 5px;
        }
        .section-title {
            text-align: center;
            color: #57737A;
            margin: 20px 0;
        }
        hr {
            border: 2px solid #57737A;
            margin: 20px 0;
        }
        .alert {
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include "nav1.php"; ?>
    
    <main id="main" class="main">

        <div class="pagetitle">
            <?php
            $id2 = $_GET['id2'];
            $id = $_GET['id'];

            if ($id2 == 1) {
                echo "<h4><a href='reject.php'>Reject Reports</a> / View Report No. ".$id."</h4>";
            } else if ($id2 == 11) {
                echo "<h4><a href='adminreport.php'>New Report</a> / View Report No. ".$id."</h4>";
            } else if ($id2 == 3) {
                echo "<h4><a href='adminoncase.php'>On-Going</a> / View Report No. ".$id."</h4>";
            } else if ($id2 == 5) {
                echo "<h4><a href='complete.php'>Completed</a> / View Report No. ".$id."</h4>";
            }
            ?>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="col-12 m">
                <div class="card recent-sales overflow-auto">

                    <div class="card-body mt-3">
                        <div class="error-message">
                        <?php
                            if (!empty($_GET['error_message'])) {
                                $co = isset($_GET['color']) ? $_GET['color'] : 'p';
                                if ($co == "p") {
                                    echo "<div class='alert' style='background-color:#57737A;color:white' role='alert'>" . $_GET['error_message'] . "</div>";
                                } else {
                                    echo "<div class='alert' style='background-color:#A4B0C7;' role='alert'>" . $_GET['error_message'] . "</div>";
                                }
                            }
                            ?>
                        </div>
                    </div>

                    <div class="card-body mt-3">
                        <div class="header" style = "background-color:#add8e6;">
                            <h3 style = "color:#184965; font-weight:bolder;">REPORT</h3>
                        </div>

                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="reportedBy" class="form-label">Reported By</label>
                                        <input type="text" class="form-control" id="reportedBy" value="<?php echo $report['userfull']; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section-title">
                            <h3 style = "color:#184965; font-weight:bolder;">CRIME INFORMATION</h3>
                        </div>

                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="crimeType" class="form-label">Type of Crime</label>
                                        <input type="text" class="form-control" id="crimeType" value="<?php echo $report['category']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="createdDate" class="form-label">Created Date</label>
                                        <input type="text" class="form-control" id="createdDate" value="<?php echo $report['file_date']; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="section-title">
                            <h3 style = "color:#184965; font-weight:bolder;">PERSONAL INFORMATION</h3>
                        </div>
                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="victimName" class="form-label">Name of Victim</label>
                                        <input type="text" class="form-control" id="victimName" value="<?php echo $report['name']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="victimContact" class="form-label">Victim Contact</label>
                                        <input type="text" class="form-control" id="victimContact" value="<?php echo $report['contact']; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="victimAddress" class="form-label">Victim Address</label>
                                        <input type="text" class="form-control" id="victimAddress" value="<?php echo $report['address']; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="witnessName" class="form-label">Name of Witness</label>
                                        <input type="text" class="form-control" id="witnessName" value="<?php echo $report['witness']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="witnessContact" class="form-label">Contact of Witness</label>
                                        <input type="text" class="form-control" id="witnessContact" value="<?php echo $report['witnessno']; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="section-title">
                            <h3 style = "color:#184965; font-weight:bolder;">REPORT DETAILS</h3>
                        </div>

                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="crimeDate" class="form-label">Crime Date</label>
                                        <input type="text" class="form-control" id="crimeDate" value="<?php echo $report['crimedate']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="crimeTime" class="form-label">Time</label>
                                        <input type="text" class="form-control" id="crimeTime" value="<?php echo $report['time']; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="container">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description of Incident</label>
                                <textarea class="form-control" id="description" rows="4" readonly><?php echo $report['description']; ?></textarea>
                            </div>
                        </div>

                        <hr>

                        <div class="section-title">
                            <h3 style = "color:#184965; font-weight:bolder;">OTHER DETAILS</h3>
                        </div>
                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="assignedOfficer" class="form-label">Assigned Officer</label>
                                        <input type="text" class="form-control" id="assignedOfficer" value="<?php echo $report['police']; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="injury" class="form-label">Injury Reported</label>
                                        <input type="text" class="form-control" id="injury" value="<?php echo $report['injury']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="reportLocation" class="form-label">Location of Incident</label>
                                        <input type="text" class="form-control" id="reportLocation" value="<?php echo $report['location']; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section-title">
                            <h3 style = "color:#184965; font-weight:bolder;">SUBMITTED FILES</h3>
                        </div>


                        <div class="container">
                        <div class="mb-3">
                            <div class="row justify-content-center text-center">
                                <div class="col-auto mx-2"> <!-- Adjust spacing with mx-2 -->
                                    <a href="uploads/<?php echo $report['valid_id']; ?>" download="Evidence_file">
                                        <?php
                                        $extension1 = pathinfo($report['valid_id'], PATHINFO_EXTENSION);

                                        if ($extension1 == "pdf") {
                                            echo "<img src='pdf.png' width='100px'>";
                                        } else {
                                            echo "<img src='docs.png' width='100px'>";
                                        }
                                        ?>
                                        <br>
                                        <?php echo $report['valid_id']; ?>
                                    </a>
                                </div>
                                <div class="col-auto mx-2"> <!-- Adjust spacing with mx-2 -->
                                    <a href="uploads/<?php echo $report['evidence']; ?>" download="Evidence_file">
                                        <?php
                                        $extension2 = pathinfo($report['evidence'], PATHINFO_EXTENSION);

                                        if ($extension2 == "pdf") {
                                            echo "<img src='pdf.png' width='100px'>";
                                        } else {
                                            echo "<img src='docs.png' width='100px'>";
                                        }
                                        ?>
                                        <br>
                                        <?php echo $report['evidence']; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="card-footer">
                        <div class="d-flex justify-content-end">
                            <a href="adminoncase.php" class="btn btn-secondary" style="background-color:#184965;">Back</a>
                        </div>
                    </div>

                </div>
            </div>

        </section>
    </main><!-- End #main -->

    <!-- Vendor JS Files -->
    <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/chart.js/chart.umd.js"></script>
    <script src="assets/vendor/echarts/echarts.min.js"></script>
    <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>
</body>
</html>
