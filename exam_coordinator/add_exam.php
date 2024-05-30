<?php
session_start();
include('config.php');

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = $_POST['course_id'];
    $hall_id = $_POST['hall_id'];
    $superintendent_id = $_POST['superintendent_id'];
    $exam_date = $_POST['exam_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Check for duplicate entry
    $sql_check = "SELECT * FROM ExamSchedule 
                  WHERE exam_date = ? 
                  AND hall_id = ? 
                  AND superintendent_id = ? 
                  AND course_id = ?
                  AND ((start_time < ? AND end_time > ?) OR (start_time < ? AND end_time > ?) OR (start_time >= ? AND end_time <= ?))";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("siisssssss", $exam_date, $hall_id, $superintendent_id, $course_id, $end_time, $start_time, $start_time, $end_time, $start_time, $end_time);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Duplicate entry found
        echo "<script>alert('Exam schedule conflicts with existing schedule'); window.location.href = 'add_exam.php';</script>";
    } else {
        // Insert the new exam schedule
        $sql = "INSERT INTO ExamSchedule (course_id, hall_id, superintendent_id, exam_date, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiisss", $course_id, $hall_id, $superintendent_id, $exam_date, $start_time, $end_time);

        if ($stmt->execute()) {
            echo "<script>alert('Exam schedule added successfully'); window.location.href = 'view_exam_schedule.php';</script>";
        } else {
            echo "<script>alert('Error adding exam schedule'); window.location.href = 'add_exam_schedule.php';</script>";
        }
        $stmt->close();
    }
    $stmt_check->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Exam Schedule</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php
include('navbar.php');
?>

<div class="container mt-5 mb-5">
    <h2>Add New Exam Schedule</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
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
        <div class="form-group">
            <label for="hall_id">Examination Hall:</label>
            <select class="form-control" id="hall_id" name="hall_id" required>
                <?php
                $sql = "SELECT hall_id, hall_name FROM ExaminationHalls";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['hall_id']}'>{$row['hall_name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="superintendent_id">Superintendent:</label>
            <select class="form-control" id="superintendent_id" name="superintendent_id" required>
                <?php
                $sql = "SELECT id, name FROM Users WHERE role = 'superintendent'";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="exam_date">Exam Date:</label>
            <input type="date" class="form-control" id="exam_date" name="exam_date" required min="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="form-group">
            <label for="start_time">Start Time:</label>
            <input type="time" class="form-control" id="start_time" name="start_time" required>
        </div>
        <div class="form-group">
            <label for="end_time">End Time:</label>
            <input type="time" class="form-control" id="end_time" name="end_time" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Schedule</button>
        <a class="btn btn-outline-dark" href="view_exam_schedule.php">View Schedule</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
