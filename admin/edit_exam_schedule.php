<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

$schedule_id = $_GET['schedule_id'];

// Fetch the existing exam schedule details
$sql = "SELECT * FROM ExamSchedule WHERE schedule_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $schedule_id);
$stmt->execute();
$result = $stmt->get_result();
$schedule = $result->fetch_assoc();
$stmt->close();

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
                  WHERE schedule_id != ? AND exam_date = ? 
                  AND hall_id = ? 
                  AND superintendent_id = ? 
                  AND course_id = ?
                  AND ((start_time < ? AND end_time > ?) OR (start_time < ? AND end_time > ?) OR (start_time >= ? AND end_time <= ?))";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("isiisssssss", $schedule_id, $exam_date, $hall_id, $superintendent_id, $course_id, $end_time, $start_time, $start_time, $end_time, $start_time, $end_time);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Duplicate entry found
        echo "<script>alert('Exam schedule conflicts with existing schedule'); window.location.href = 'edit_exam_schedule.php?schedule_id=$schedule_id';</script>";
    } else {
        // Update the exam schedule
        $sql = "UPDATE ExamSchedule SET course_id = ?, hall_id = ?, superintendent_id = ?, exam_date = ?, start_time = ?, end_time = ? WHERE schedule_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiisssi", $course_id, $hall_id, $superintendent_id, $exam_date, $start_time, $end_time, $schedule_id);

        if ($stmt->execute()) {
            echo "<script>alert('Exam schedule updated successfully'); window.location.href = 'view_exam_schedule.php';</script>";
        } else {
            echo "<script>alert('Error updating exam schedule'); window.location.href = 'edit_exam_schedule.php?schedule_id=$schedule_id';</script>";
        }
        $stmt->close();
    }
    $stmt_check->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Exam Schedule</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2>Edit Exam Schedule</h2>
    <form action="<?php echo $_SERVER['PHP_SELF'] . '?schedule_id=' . $schedule_id; ?>" method="POST">
        <div class="form-group">
            <label for="course_id">Course:</label>
            <select class="form-control" id="course_id" name="course_id" required>
                <?php
                $sql = "SELECT course_id, course_name FROM Courses";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    $selected = $row['course_id'] == $schedule['course_id'] ? 'selected' : '';
                    echo "<option value='{$row['course_id']}' $selected>{$row['course_name']}</option>";
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
                    $selected = $row['hall_id'] == $schedule['hall_id'] ? 'selected' : '';
                    echo "<option value='{$row['hall_id']}' $selected>{$row['hall_name']}</option>";
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
                    $selected = $row['id'] == $schedule['superintendent_id'] ? 'selected' : '';
                    echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="exam_date">Exam Date:</label>
            <input type="date" class="form-control" id="exam_date" name="exam_date" required min="<?php echo date('Y-m-d'); ?>" value="<?php echo $schedule['exam_date']; ?>">
        </div>
        <div class="form-group">
            <label for="start_time">Start Time:</label>
            <input type="time" class="form-control" id="start_time" name="start_time" required value="<?php echo $schedule['start_time']; ?>">
        </div>
        <div class="form-group">
            <label for="end_time">End Time:</label>
            <input type="time" class="form-control" id="end_time" name="end_time" required value="<?php echo $schedule['end_time']; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update Schedule</button>
        <a class="btn btn-outline-dark" href="view_exam_schedule.php">Back to Schedule</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
