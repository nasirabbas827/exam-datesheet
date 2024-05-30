<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hall_name = $_POST['hall_name'];
    $capacity = $_POST['capacity'];

    // Insert the new hall
    $sql = "INSERT INTO ExaminationHalls (hall_name, capacity) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hall_name, $capacity);

    if ($stmt->execute()) {
        echo "<script>alert('Examination hall added successfully'); window.location.href = 'view_halls.php';</script>";
    } else {
        echo "<script>alert('Error adding examination hall'); window.location.href = 'add_hall.php';</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Examination Hall</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2>Add New Examination Hall</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <div class="form-group">
            <label for="hall_name">Hall Name:</label>
            <input type="text" class="form-control" id="hall_name" name="hall_name" required>
        </div>
        <div class="form-group">
            <label for="capacity">Capacity:</label>
            <input type="number" class="form-control" id="capacity" name="capacity" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Hall</button>
        <a class="btn btn-outline-dark" href="view_halls.php">View Halls</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
