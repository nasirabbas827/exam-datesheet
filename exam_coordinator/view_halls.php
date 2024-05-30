<?php
session_start();
include('config.php');

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Handle delete hall request
if (isset($_GET['delete'])) {
    $hall_id = $_GET['delete'];
    $sql = "DELETE FROM ExaminationHalls WHERE hall_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $hall_id);
    if ($stmt->execute()) {
        echo "<script>alert('Examination hall deleted successfully'); window.location.href = 'view_halls.php';</script>";
    } else {
        echo "<script>alert('Error deleting examination hall'); window.location.href = 'view_halls.php';</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Examination Halls</title>
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
    <h2>All Examination Halls</h2>
    <a href="add_hall.php" class="btn btn-success mb-3">Add New Hall</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Hall Name</th>
                <th>Capacity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch all examination halls
            $sql = "SELECT * FROM ExaminationHalls";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['hall_id']}</td>
                            <td>{$row['hall_name']}</td>
                            <td>{$row['capacity']}</td>
                            <td>
                                <a href='edit_hall.php?hall_id={$row['hall_id']}' class='btn btn-warning btn-sm'>Edit</a>
                                <a href='view_halls.php?delete={$row['hall_id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this hall?');\">Delete</a>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No examination halls found</td></tr>";
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
