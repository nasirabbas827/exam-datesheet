<?php
session_start();
include('config.php');

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enrollment_id = $_POST['enrollment_id'];

    // Prepare and execute the SQL delete statement
    $stmt = $conn->prepare("DELETE FROM Enrollments WHERE enrollment_id = ?");
    $stmt->bind_param("i", $enrollment_id);

    if ($stmt->execute()) {
        // Redirect to the enrollments page with a success message
        header("Location: view_enrollments.php?msg=Enrollment deleted successfully");
    } else {
        // Redirect to the enrollments page with an error message
        header("Location: view_enrollments.php?msg=Error deleting enrollment");
    }

    $stmt->close();
}

$conn->close();
?>
