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

$enrolled_courses = [];
while ($row = $result_courses->fetch_assoc()) {
    $enrolled_courses[] = $row;
}

// Fetch exam schedules for the enrolled courses
$exam_schedules = [];
foreach ($enrolled_courses as $course) {
    $course_id = $course['course_id'];
    $sql_exams = "SELECT es.exam_date, es.start_time, es.end_time, h.hall_name, u.name AS superintendent_name
                  FROM ExamSchedule es 
                  JOIN ExaminationHalls h ON es.hall_id = h.hall_id
                  JOIN Users u ON es.superintendent_id = u.id
                  WHERE es.course_id = ?";
    $stmt_exams = $conn->prepare($sql_exams);
    $stmt_exams->bind_param("i", $course_id);
    $stmt_exams->execute();
    $result_exams = $stmt_exams->get_result();
    
    // Store exam schedules in array
    while ($exam_row = $result_exams->fetch_assoc()) {
        $exam_schedules[] = [
            'course_code' => $course['course_code'],
            'course_name' => $course['course_name'],
            'exam_date' => $exam_row['exam_date'],
            'start_time' => $exam_row['start_time'],
            'end_time' => $exam_row['end_time'],
            'hall_name' => $exam_row['hall_name'],
            'superintendent_name' => $exam_row['superintendent_name']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
</head>

<body>
    <?php include("navbar.php"); ?>
    <div class="container mt-5">
        <h1>Welcome, Student!</h1>
        <p>This is your dashboard. You can view your enrolled courses and exam schedules here.</p>
        <h3>Your Enrolled Courses:</h3>
        <div class="row">
            <?php foreach ($enrolled_courses as $course): ?>
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $course['course_name']; ?></h5>
                            <p class="card-text">Course Code: <?php echo $course['course_code']; ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <h3>Your Exam Schedule:</h3>
        <table id="examScheduleTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Exam Hall</th>
                    <th>Superintendent</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exam_schedules as $schedule): ?>
                    <tr>
                        <td><?php echo $schedule['course_code']; ?></td>
                        <td><?php echo $schedule['course_name']; ?></td>
                        <td><?php echo $schedule['exam_date']; ?></td>
                        <td><?php echo $schedule['start_time']; ?></td>
                        <td><?php echo $schedule['end_time']; ?></td>
                        <td><?php echo $schedule['hall_name']; ?></td>
                        <td><?php echo $schedule['superintendent_name']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#examScheduleTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'pdf', 'print'
            ]
        });
    });
    </script>
</body>

</html>
