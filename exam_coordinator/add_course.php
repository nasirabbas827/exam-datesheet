<?php
session_start();
include('config.php');

// Check if the user is an exam coordinator
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get course code from form input
    $course_code = $_POST['course_code'];

    // Check if a file is uploaded
    if (isset($_FILES['enrollment_file']) && $_FILES['enrollment_file']['error'] === UPLOAD_ERR_OK) {
        // File details
        $fileTmpPath = $_FILES['enrollment_file']['tmp_name'];
        $fileName = $_FILES['enrollment_file']['name'];
        $fileType = $_FILES['enrollment_file']['type'];

        // Make sure it is a text file
        if ($fileType == 'text/plain') {
            // Open the file and read each line (each line is a student ID)
            $file = fopen($fileTmpPath, "r");

            // Insert course into the courses table
            $stmt = $conn->prepare("INSERT INTO courses (course_code) VALUES (?)");
            $stmt->bind_param("s", $course_code);
            if ($stmt->execute()) {
                // Get the course_id of the inserted course
                $course_id = $conn->insert_id;

                // Insert each student ID from the text file into the enrollments table
                $stmt_enroll = $conn->prepare("INSERT INTO enrollments (course_id, student_id) VALUES (?, ?)");

                while (($student_id = fgets($file)) !== false) {
                    $student_id = trim($student_id); // Clean up any extra whitespace/newlines
                    if (!empty($student_id)) {
                        $stmt_enroll->bind_param("is", $course_id, $student_id);
                        $stmt_enroll->execute();
                    }
                }

                fclose($file);
                echo "<div class='alert alert-success'>Course and enrollments added successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error adding course: " . $stmt->error . "</div>";
            }

            $stmt->close();
        } else {
            echo "<div class='alert alert-danger'>Please upload a valid text file.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Please upload a file.</div>";
    }
}

$conn->close();
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

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Add a New Course and Enrollments</h2>
    <form action="add_course.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="course_code">Course Code:</label>
            <input type="text" class="form-control" id="course_code" name="course_code" required>
        </div>
        <div class="form-group">
            <label for="enrollment_file">Upload Enrollment File (.txt):</label>
            <input type="file" class="form-control" id="enrollment_file" name="enrollment_file" accept=".txt" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Course</button>
        <a class="btn btn-outline-dark" href="manage_courses.php">Manage Courses and Enrollments</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
