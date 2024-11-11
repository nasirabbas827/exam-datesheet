<?php
session_start();
include('config.php');

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

// Fetch user details
$user_id = $_SESSION["id"];
$user_query = $conn->prepare("SELECT name, designation, department, email FROM superintendents WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user_data = $user_result->fetch_assoc();

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
<html lang="en">

<head>
    <title>View Exam Schedule</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <?php include('navbar.php'); ?>

    <div class="container mt-5 mb-5">
        <h2>Welcome, <?= htmlspecialchars($user_data['name']) ?>!</h2>
        <p><strong>Designation:</strong> <?= htmlspecialchars($user_data['designation']) ?></p>
        <p><strong>Department:</strong> <?= htmlspecialchars($user_data['department']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user_data['email']) ?></p>

        <h2 class="mt-4">Exam Schedule</h2>

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
                        <td><?= htmlspecialchars($row['course_code']) ?></td>
                        <td>Day <?= htmlspecialchars($row['day']) ?></td>
                        <td><?= htmlspecialchars($row['slot']) ?></td>
                        <td><?= htmlspecialchars($row['time_range']) ?></td>
                        <td><?= htmlspecialchars($row['superintendent']) ?></td>
                        <td><?= htmlspecialchars($row['hall_number']) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No schedule found.</p>
        <?php endif; ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
