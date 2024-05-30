<?php
session_start();
include('config.php');

// Redirect to login page if user is not logged in or not a student
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
    header("Location: login.php");
    exit;
}

// Fetch student ID
$student_id = $_SESSION["id"];

// Fetch enrolled courses for the student
$sql_courses = "SELECT c.course_id, c.course_code, c.course_name 
                FROM Enrollments e 
                JOIN Courses c ON e.course_id = c.course_id 
                WHERE e.student_id = ?";
$stmt_courses = $conn->prepare($sql_courses);
$stmt_courses->bind_param("i", $student_id);
$stmt_courses->execute();
$result_courses = $stmt_courses->get_result();

// Fetch exam schedules for the enrolled courses
$notifications = [];
while ($row = $result_courses->fetch_assoc()) {
    $course_id = $row['course_id'];
    $sql_exams = "SELECT es.exam_date, es.start_time, es.end_time, h.hall_name
                  FROM ExamSchedule es 
                  JOIN ExaminationHalls h ON es.hall_id = h.hall_id
                  WHERE es.course_id = ?";
    $stmt_exams = $conn->prepare($sql_exams);
    $stmt_exams->bind_param("i", $course_id);
    $stmt_exams->execute();
    $result_exams = $stmt_exams->get_result();
    
    // Store exam schedules in array
    while ($exam_row = $result_exams->fetch_assoc()) {
        $notifications[] = [
            'course_code' => $row['course_code'],
            'course_name' => $row['course_name'],
            'exam_date' => $exam_row['exam_date'],
            'start_time' => $exam_row['start_time'],
            'end_time' => $exam_row['end_time'],
            'hall_name' => $exam_row['hall_name']
        ];
    }
}

// Reset the internal pointer of the result set
$stmt_courses->data_seek(0);
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
        <p>Below are your exam schedules:</p>
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
