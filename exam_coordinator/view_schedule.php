<?php
session_start();
include('config.php');

// Check if the user is logged in and has the right role
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_schedule'])) {
    $conn->query("DELETE FROM exam_schedule");
    $message = "All schedules have been deleted successfully!";
}

// Fetch the current schedule
$schedule_result = $conn->query("
    SELECT es.id, c.course_code, es.day, es.slot, es.time_range, s.name as superintendent, es.hall_number
    FROM exam_schedule es
    JOIN courses c ON es.course_id = c.id
    LEFT JOIN superintendents s ON es.superintendent_id = s.id
    ORDER BY es.day, es.slot
");

?>

<!DOCTYPE html>
<html>

<head>
    <title>View Exam Schedule</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Exam Schedule</h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>

    <?php if ($schedule_result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Course Code</th>
                <th>Day</th>
                <th>Slot</th>
                <th>Time</th>
                <th>Superintendent</th>
                <th>Exam Hall</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $schedule_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['course_code'] ?></td>
                    <td>Day <?= $row['day'] ?></td>
                    <td><?= $row['slot'] ?></td>
                    <td><?= $row['time_range'] ?></td>
                    <td><?= $row['superintendent'] ?></td>
                    <td><?= $row['hall_number'] ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <form method="post" onsubmit="return confirm('Are you sure you want to delete all schedules? This action cannot be undone.')">
            <button type="submit" name="delete_schedule" class="btn btn-danger float-right">Delete All Schedule</button>
        </form>
    <?php else: ?>
        <p>No schedule found.</p>
    <?php endif; ?>
</div>
</body>

</html>
