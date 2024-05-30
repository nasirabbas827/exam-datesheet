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

$assigned_schedules = [];
while ($row = $result_schedules->fetch_assoc()) {
    $assigned_schedules[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superintendent Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h1>Welcome, Superintendent!</h1>
        <p>This is your dashboard. You can manage examination halls, exam schedules, etc. here.</p>

        <h3>Your Assigned Exam Schedules:</h3>
        <table id="assignedSchedulesTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Schedule ID</th>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Exam Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Exam Hall</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assigned_schedules as $schedule): ?>
                    <tr>
                        <td><?php echo $schedule['schedule_id']; ?></td>
                        <td><?php echo $schedule['course_code']; ?></td>
                        <td><?php echo $schedule['course_name']; ?></td>
                        <td><?php echo $schedule['exam_date']; ?></td>
                        <td><?php echo $schedule['start_time']; ?></td>
                        <td><?php echo $schedule['end_time']; ?></td>
                        <td><?php echo $schedule['hall_name']; ?></td>
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
        $('#assignedSchedulesTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'pdf', 'print'
            ]
        });
    });
    </script>
</body>

</html>
