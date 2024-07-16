<?php
session_start();
include('config.php');

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['course_id'])) {
    $course_id = $_POST['course_id'];
    $course_code = $_POST['course_code'];
    $course_name = $_POST['course_name'];
    $faculty_id = $_POST['faculty_id']; // Added to handle faculty update

    // Update the course details including faculty_id
    $sql = "UPDATE Courses SET course_code=?, course_name=?, faculty_id=? WHERE course_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $course_code, $course_name, $faculty_id, $course_id);

    if ($stmt->execute()) {
        echo "<script>alert('Course updated successfully'); window.location.href = 'view_courses.php';</script>";
    } else {
        echo "<script>alert('Error updating course'); window.location.href = 'edit_course.php?course_id={$course_id}';</script>";
    }
    $stmt->close();
}

// Fetch course details
if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
    $sql = "SELECT c.course_id, c.course_code, c.course_name, c.faculty_id, f.name AS faculty_name
            FROM Courses c
            LEFT JOIN Faculty f ON c.faculty_id = f.id
            WHERE c.course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();
    $stmt->close();
} else {
    header("Location: view_courses.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Course</title>
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
    <h2>Edit Course</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
        <div class="form-group">
            <label for="course_code">Course Code:</label>
            <input type="text" class="form-control" id="course_code" name="course_code" value="<?php echo $course['course_code']; ?>" required>
        </div>
        <div class="form-group">
            <label for="course_name">Course Name:</label>
            <input type="text" class="form-control" id="course_name" name="course_name" value="<?php echo $course['course_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="faculty_id">Faculty:</label>
            <select class="form-control" id="faculty_id" name="faculty_id" required>
                <?php
                // Fetch and display all faculty members
                $sql = "SELECT id, name FROM Faculty";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    $selected = ($row['id'] == $course['faculty_id']) ? 'selected' : '';
                    echo "<option value='" . $row['id'] . "' $selected>" . $row['name'] . "</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Course</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
