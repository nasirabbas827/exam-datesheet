<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Function to fetch all exam schedules
function getExamSchedules($conn) {
    $sql = "SELECT id, day_number, courses, superintendent, hall_name
            FROM ExamSchedule";
    $result = $conn->query($sql);
    
    $schedules = [];
    while ($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }
    
    return $schedules;
}

// Function to delete an exam schedule by schedule_id
function deleteExamSchedule($schedule_id, $conn) {
    $sql = "DELETE FROM ExamSchedule WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $schedule_id);
    $stmt->execute();
    $stmt->close();
}

// Function to delete all exam schedules
function deleteAllExamSchedules($conn) {
    $sql = "DELETE FROM ExamSchedule";
    $conn->query($sql);
}

// Fetch all exam schedules
$schedules = getExamSchedules($conn);

// Handle delete schedule request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_schedule'])) {
        $schedule_id = $_POST['schedule_id'];
        deleteExamSchedule($schedule_id, $conn);
    } elseif (isset($_POST['delete_all_schedules'])) {
        deleteAllExamSchedules($conn);
    }
    // Redirect to same page to avoid resubmission on refresh
    header("Location: view_schedule.php");
    exit;
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

<?php include('admin_navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2>View Exam Schedule</h2>
    <?php if (!empty($schedules)): ?>
        <div class="table-responsive mt-4">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Courses</th>
                        <th>Superintendent</th>
                        <th>Exam Hall</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $schedule): ?>
                        <tr>
                            <td><?php echo $schedule['day_number']; ?></td>
                            <td><?php echo $schedule['courses']; ?></td>
                            <td><?php echo $schedule['superintendent']; ?></td>
                            <td><?php echo $schedule['hall_name']; ?></td>
                            
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <form method="post">
        </form>
    <?php else: ?>
        <div class="alert alert-info mt-4">
            No exam schedules found.
        </div>
    <?php endif; ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
