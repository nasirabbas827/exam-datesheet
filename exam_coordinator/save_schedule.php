<?php
session_start();
include('config.php');

// Check if the user is logged in and has the right role
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Check if schedule data exists in session
if (!isset($_SESSION['schedule_data'])) {
    echo "No schedule to save!";
    exit;
}

$schedule_data = $_SESSION['schedule_data'];
foreach ($schedule_data as $entry) {
    $course_id = $entry['course_id'];
    $day = $entry['day'];
    $slot = $entry['slot'];
    $time_range = $entry['time_range'];
    $superintendent_id = $entry['superintendent_id'];
    $hall_number = $entry['hall_number'];

    // Insert the schedule into the exam_schedule table
    $stmt = $conn->prepare("
        INSERT INTO exam_schedule (course_id, day, slot, time_range, superintendent_id, hall_number)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iissis", $course_id, $day, $slot, $time_range, $superintendent_id, $hall_number);
    $stmt->execute();
}

echo "Schedule saved successfully!";
header("Location: view_schedule.php");
exit;
?>
