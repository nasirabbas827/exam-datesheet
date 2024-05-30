<?php
session_start();
include('config.php');

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Handle delete request
if (isset($_POST['delete'])) {
    $schedule_id = $_POST['schedule_id'];

    $sql_delete = "DELETE FROM ExamSchedule WHERE schedule_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $schedule_id);
    if ($stmt_delete->execute()) {
        echo "<script>alert('Exam schedule deleted successfully'); window.location.href = 'view_exam_schedule.php';</script>";
    } else {
        echo "<script>alert('Error deleting exam schedule'); window.location.href = 'view_exam_schedule.php';</script>";
    }
    $stmt_delete->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Exam Schedule</title>
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
    <h2>All Exam Schedules</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Schedule ID</th>
                <th>Course Name</th>
                <th>Hall Name</th>
                <th>Superintendent</th>
                <th>Exam Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch all exam schedules with details
            $sql = "SELECT es.schedule_id, c.course_name, eh.hall_name, u.name AS superintendent_name, es.exam_date, es.start_time, es.end_time
                    FROM ExamSchedule es
                    INNER JOIN Courses c ON es.course_id = c.course_id
                    INNER JOIN ExaminationHalls eh ON es.hall_id = eh.hall_id
                    INNER JOIN Users u ON es.superintendent_id = u.id";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['schedule_id']}</td>
                            <td>{$row['course_name']}</td>
                            <td>{$row['hall_name']}</td>
                            <td>{$row['superintendent_name']}</td>
                            <td>{$row['exam_date']}</td>
                            <td>{$row['start_time']}</td>
                            <td>{$row['end_time']}</td>
                            <td>
                                <form method='post' style='display:inline-block;'>
                                    <input type='hidden' name='schedule_id' value='{$row['schedule_id']}'>
                                    <button type='submit' name='delete' class='btn btn-danger btn-sm'>Delete</button>
                                </form>
                                <a href='edit_exam_schedule.php?schedule_id={$row['schedule_id']}' class='btn btn-warning btn-sm'>Edit</a>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No exam schedules found</td></tr>";
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
