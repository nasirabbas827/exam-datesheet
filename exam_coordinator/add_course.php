<?php
session_start();
include('config.php');

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_code = $_POST['course_code'];
    $course_name = $_POST['course_name'];

    // Check for existing course code
    $sql = "SELECT * FROM Courses WHERE course_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $course_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Course code already exists'); window.location.href = 'add_course.php';</script>";
    } else {
        // Insert the new course
        $sql = "INSERT INTO Courses (course_code, course_name) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $course_code, $course_name);

        if ($stmt->execute()) {
            echo "<script>alert('Course added successfully'); window.location.href = 'view_courses.php';</script>";
        } else {
            echo "<script>alert('Error adding course'); window.location.href = 'add_course.php';</script>";
        }
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Course</title>
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
    <h2>Add New Course</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <div class="form-group">
            <label for="course_code">Course Code:</label>
            <input type="text" class="form-control" id="course_code" name="course_code" required>
        </div>
        <div class="form-group">
            <label for="course_name">Course Name:</label>
            <input type="text" class="form-control" id="course_name" name="course_name" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Course</button>
        <a class="btn btn-outline-dark" href="view_courses.php">View Courses</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
