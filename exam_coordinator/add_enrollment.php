<?php
session_start();
include('config.php');

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];

    // Check if the student is already enrolled in the course
    $check_sql = "SELECT * FROM Enrollments WHERE student_id = ? AND course_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $student_id, $course_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Student is already enrolled in the course
        echo "<script>alert('The student is already enrolled in this course'); window.location.href = 'add_enrollment.php';</script>";
    } else {
        // Insert the new enrollment
        $sql = "INSERT INTO Enrollments (student_id, course_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $student_id, $course_id);

        if ($stmt->execute()) {
            echo "<script>alert('Enrollment added successfully'); window.location.href = 'view_enrollments.php';</script>";
        } else {
            echo "<script>alert('Error adding enrollment'); window.location.href = 'add_enrollment.php';</script>";
        }
        $stmt->close();
    }
    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Enrollment</title>
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
    <h2>Add New Enrollment</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <div class="form-group">
            <label for="student_id">Student:</label>
            <select class="form-control" id="student_id" name="student_id" required>
                <?php
                $sql = "SELECT id, name FROM Users WHERE role = 'student'";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="course_id">Course:</label>
            <select class="form-control" id="course_id" name="course_id" required>
                <?php
                $sql = "SELECT course_id, course_name FROM Courses";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['course_id']}'>{$row['course_name']}</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Enrollment</button>
        <a class="btn btn-outline-dark" href="view_enrollments.php">View Enrollments</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
