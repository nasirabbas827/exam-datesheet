<?php
session_start();
include('config.php');

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Fetch enrollments data
$sql = "SELECT e.enrollment_id, e.course_id, c.course_name, e.student_id
        FROM Enrollments e
        INNER JOIN Courses c ON e.course_id = c.course_id
        ORDER BY e.course_id, e.enrollment_id";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Enrollments</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>View Enrollments</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Enrollment ID</th>
                    <th>Course ID</th>
                    <th>Course Name</th>
                    <th>Student ID</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['enrollment_id']}</td>";
                        echo "<td>{$row['course_id']}</td>";
                        echo "<td>{$row['course_name']}</td>";
                        echo "<td>{$row['student_id']}</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No enrollments found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <a class="btn btn-primary" href="update_enrollment.php">Update Enrollment</a>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
