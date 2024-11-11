<?php
session_start();
include('config.php');

// Check if the user is an exam coordinator
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update enrollments for a course
    if (isset($_POST['update_course'])) {
        $course_id = $_POST['course_id'];

        // Check if a file is uploaded
        if (isset($_FILES['enrollment_file']) && $_FILES['enrollment_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['enrollment_file']['tmp_name'];
            $fileType = $_FILES['enrollment_file']['type'];

            // Make sure it is a text file
            if ($fileType == 'text/plain') {
                $file = fopen($fileTmpPath, "r");

                // First, delete the current enrollments for the course
                $stmt = $conn->prepare("DELETE FROM enrollments WHERE course_id = ?");
                $stmt->bind_param("i", $course_id);
                $stmt->execute();

                // Then, insert the new enrollments from the file
                $stmt_enroll = $conn->prepare("INSERT INTO enrollments (course_id, student_id) VALUES (?, ?)");

                while (($student_id = fgets($file)) !== false) {
                    $student_id = trim($student_id);
                    if (!empty($student_id)) {
                        $stmt_enroll->bind_param("is", $course_id, $student_id);
                        $stmt_enroll->execute();
                    }
                }

                fclose($file);
                echo "<div class='alert alert-success'>Enrollments updated successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Please upload a valid text file.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Please upload a file.</div>";
        }
    }

    // Delete a course
    if (isset($_POST['delete_course'])) {
        $course_id = $_POST['course_id'];

        // Delete course and associated enrollments
        $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->bind_param("i", $course_id);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Course and enrollments deleted successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error deleting course: " . $stmt->error . "</div>";
        }
    }
}

// Fetch all courses for display
$courses = $conn->query("SELECT * FROM courses");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Courses</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Manage Courses</h2>

    <!-- Display list of all courses -->
    <h4>All Courses</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Course ID</th>
                <th>Course Code</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($course = $courses->fetch_assoc()): ?>
            <tr>
                <td><?php echo $course['id']; ?></td>
                <td><?php echo $course['course_code']; ?></td>
                <td>
                    <!-- Update enrollments form -->
                    <form action="manage_courses.php" method="post" enctype="multipart/form-data" style="display:inline-block;">
                        <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                        <input type="file" name="enrollment_file" accept=".txt" required>
                        <button type="submit" name="update_course" class="btn btn-sm btn-warning">Update Enrollments</button>
                    </form>

                    <!-- Delete course form -->
                    <form action="manage_courses.php" method="post" style="display:inline-block;">
                        <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                        <button type="submit" name="delete_course" class="btn btn-sm btn-danger">Delete Course</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
