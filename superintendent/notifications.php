<?php
session_start();
include('config.php');

// Redirect to login page if user is not logged in or not a superintendent
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "superintendent") {
    header("Location: login.php");
    exit;
}

// Fetch superintendent ID
$superintendent_id = $_SESSION["id"];

// Fetch assigned exam schedules for the superintendent
$sql_schedules = "SELECT es.schedule_id, c.course_code, c.course_name, es.exam_date, es.start_time, es.end_time, h.hall_name
                  FROM ExamSchedule es
                  JOIN Courses c ON es.course_id = c.course_id
                  JOIN ExaminationHalls h ON es.hall_id = h.hall_id
                  WHERE es.superintendent_id = ?";
$stmt_schedules = $conn->prepare($sql_schedules);
$stmt_schedules->bind_param("i", $superintendent_id);
$stmt_schedules->execute();
$result_schedules = $stmt_schedules->get_result();

$notifications = [];
while ($row = $result_schedules->fetch_assoc()) {
    $notifications[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Schedule Notifications</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h1>Exam Schedule Notifications</h1>
        <p>Below are your assigned exam schedules:</p>
        <div class="list-group">
            <?php foreach ($notifications as $notification): ?>
                <a href="#" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><?php echo $notification['course_code'] . ' - ' . $notification['course_name']; ?></h5>
                        <small><?php echo $notification['exam_date'] . ' ' . $notification['start_time']; ?></small>
                    </div>
                    <p class="mb-1">Exam Hall: <?php echo $notification['hall_name']; ?></p>
                    <small>End Time: <?php echo $notification['end_time']; ?></small>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
