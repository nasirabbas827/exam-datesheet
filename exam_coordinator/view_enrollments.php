<?php
session_start();
include('config.php');

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Handle delete enrollment request
if (isset($_GET['delete'])) {
    $enrollment_id = $_GET['delete'];
    $sql = "DELETE FROM Enrollments WHERE enrollment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $enrollment_id);
    if ($stmt->execute()) {
        echo "<script>alert('Enrollment deleted successfully'); window.location.href = 'view_enrollments.php';</script>";
    } else {
        echo "<script>alert('Error deleting enrollment'); window.location.href = 'view_enrollments.php';</script>";
    }
    $stmt->close();
}
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

<?php
include('navbar.php');
?>

<div class="container mt-5">
    <h2>All Enrollments</h2>
    <a href="add_enrollment.php" class="btn btn-success mb-3">Add New Enrollment</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Student Name</th>
                <th>Course Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT e.enrollment_id, u.name as student_name, c.course_name
                    FROM Enrollments e
                    JOIN Users u ON e.student_id = u.id
                    JOIN Courses c ON e.course_id = c.course_id";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['enrollment_id']}</td>
                        <td>{$row['student_name']}</td>
                        <td>{$row['course_name']}</td>
                        <td>
                            <a href='edit_enrollment.php?enrollment_id={$row['enrollment_id']}' class='btn btn-warning btn-sm'>Edit</a>
                            <a href='view_enrollments.php?delete={$row['enrollment_id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this enrollment?');\">Delete</a>
                        </td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
