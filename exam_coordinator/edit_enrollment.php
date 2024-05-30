<?php
session_start();
include('config.php');

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enrollment_id = $_POST['enrollment_id'];
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];

    // Update the enrollment
    $sql = "UPDATE Enrollments SET student_id = ?, course_id = ? WHERE enrollment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $student_id, $course_id, $enrollment_id);

    if ($stmt->execute()) {
        echo "<script>alert('Enrollment updated successfully'); window.location.href = 'view_enrollments.php';</script>";
    } else {
        echo "<script>alert('Error updating enrollment'); window.location.href = 'edit_enrollment.php?enrollment_id={$enrollment_id}';</script>";
    }
    $stmt->close();
}

// Fetch the enrollment details
if (isset($_GET['enrollment_id'])) {
    $enrollment_id = $_GET['enrollment_id'];
    $sql = "SELECT * FROM Enrollments WHERE enrollment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $enrollment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $enrollment = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Enrollment</title>
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
    <h2>Edit Enrollment</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <input type="hidden" name="enrollment_id" value="<?php echo $enrollment['enrollment_id']; ?>">
        <div class="form-group">
            <label for="student_id">Student:</label>
            <select class="form-control" id="student_id" name="student_id" required>
                <?php
                $sql = "SELECT id, name FROM Users WHERE role = 'student'";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    $selected = $row['id'] == $enrollment['student_id'] ? "selected" : "";
                    echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
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
                    $selected = $row['course_id'] == $enrollment['course_id'] ? "selected" : "";
                    echo "<option value='{$row['course_id']}' $selected>{$row['course_name']}</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Enrollment</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
