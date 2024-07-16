<?php
session_start();
include('config.php');

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Handle update form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = $_POST['course_id'];

    // Process uploaded file
    if ($_FILES['student_file']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['student_file']['tmp_name'])) {
        $file_content = file_get_contents($_FILES['student_file']['tmp_name']);

        // Extract student IDs from file content
        $student_ids = explode("\n", $file_content);
        $student_ids = array_map('trim', $student_ids); // Remove leading/trailing whitespace

        // Delete existing enrollments for the course
        $delete_sql = "DELETE FROM Enrollments WHERE course_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $course_id);
        $delete_stmt->execute();
        $delete_stmt->close();

        // Insert each student ID into Enrollments table for the selected course
        foreach ($student_ids as $student_id) {
            // Validate student ID format (assuming BC160300123 format)
            if (preg_match('/^BC\d{9}$/', $student_id)) {
                // Insert the new enrollment
                $insert_sql = "INSERT INTO Enrollments (course_id, student_id) VALUES (?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("is", $course_id, $student_id);

                if ($insert_stmt->execute()) {
                    echo "<script>alert('Enrollment updated successfully'); window.location.href = 'view_enrollments.php';</script>";
                } else {
                    echo "<script>alert('Error updating enrollment'); window.location.href = 'update_enrollment.php';</script>";
                }
                $insert_stmt->close();
            } else {
                echo "<script>alert('Invalid student ID format: $student_id'); window.location.href = 'update_enrollment.php';</script>";
            }
        }
    } else {
        echo "<script>alert('Failed to upload file'); window.location.href = 'update_enrollment.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Enrollment</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Update Enrollment</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group mt-3">
            <label for="course_id">Select Course:</label>
            <select class="form-control" id="course_id" name="course_id" required>
                <?php
                $course_sql = "SELECT course_id, course_name FROM Courses";
                $course_result = $conn->query($course_sql);
                while ($course_row = $course_result->fetch_assoc()) {
                    echo "<option value='{$course_row['course_id']}'>{$course_row['course_name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="student_file">Upload Text File:</label>
            <input type="file" class="form-control-file" id="student_file" name="student_file" accept=".txt" required>
            <small class="form-text text-muted">Upload a .txt file containing student IDs (one per line).</small>
        </div>
        <button type="submit" class="btn btn-primary">Update Enrollment</button>
        <a class="btn btn-outline-dark ml-2" href="view_enrollments.php">Cancel</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
