<?php
session_start();
include 'config.php';

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Fetch data for dashboard
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'];
$totalHalls = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM exam_halls"))['total'];
$totalCourses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM Courses"))['total'];
$totalEnrollments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM Enrollments"))['total'];
$totalSuperintendents = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM superintendents"))['total'];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .card {
            margin-top: 30px;
        }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Dashboard</h2>
    <div class="row mt-4">
        <!-- Total Users Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text"><?php echo $totalUsers; ?></p>
                </div>
            </div>
        </div>
        <!-- Total Courses Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Courses</h5>
                    <p class="card-text"><?php echo $totalCourses; ?></p>
                </div>
            </div>
        </div>
        <!-- Total Halls Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Halls</h5>
                    <p class="card-text"><?php echo $totalHalls; ?></p>
                </div>
            </div>
        </div>
        <!-- Total Enrollments Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Enrollments</h5>
                    <p class="card-text"><?php echo $totalEnrollments; ?></p>
                </div>
            </div>
        </div>
        <!-- Total Superintendents Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Superintendents</h5>
                    <p class="card-text"><?php echo $totalSuperintendents; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
