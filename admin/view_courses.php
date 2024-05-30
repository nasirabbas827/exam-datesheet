<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Handle delete course request
if (isset($_GET['delete'])) {
    $course_id = $_GET['delete'];
    $sql = "DELETE FROM Courses WHERE course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $course_id);
    if ($stmt->execute()) {
        echo "<script>alert('Course deleted successfully'); window.location.href = 'view_courses.php';</script>";
    } else {
        echo "<script>alert('Error deleting course'); window.location.href = 'view_courses.php';</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Courses</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php
include('admin_navbar.php');
?>

<div class="container mt-5">
    <h2>All Courses</h2>
    <a href="add_course.php" class="btn btn-success mb-3">Add New Course</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Course Code</th>
                <th>Course Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM Courses";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['course_id']}</td>
                        <td>{$row['course_code']}</td>
                        <td>{$row['course_name']}</td>
                        <td>
                            <a href='edit_course.php?course_id={$row['course_id']}' class='btn btn-primary btn-sm'>Edit</a>
                            <a href='view_courses.php?delete={$row['course_id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this course?');\">Delete</a>
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
