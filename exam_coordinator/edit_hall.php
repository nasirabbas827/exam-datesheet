<?php
session_start();
include('config.php');

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Initialize variables
$hall_id = $hall_name = $capacity = '';

// Fetch hall details if hall_id is provided
if (isset($_GET['hall_id'])) {
    $hall_id = $_GET['hall_id'];
    $sql = "SELECT * FROM ExaminationHalls WHERE hall_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $hall_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hall_name = $row['hall_name'];
        $capacity = $row['capacity'];
    } else {
        echo "Hall not found";
        exit;
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hall_id = $_POST['hall_id'];
    $hall_name = $_POST['hall_name'];
    $capacity = $_POST['capacity'];

    // Update hall details
    $sql = "UPDATE ExaminationHalls SET hall_name = ?, capacity = ? WHERE hall_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $hall_name, $capacity, $hall_id);

    if ($stmt->execute()) {
        echo "<script>alert('Examination hall updated successfully'); window.location.href = 'view_halls.php';</script>";
    } else {
        echo "<script>alert('Error updating examination hall'); window.location.href = 'edit_hall.php?hall_id={$hall_id}';</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Examination Hall</title>
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
    <h2>Edit Examination Hall</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <input type="hidden" name="hall_id" value="<?php echo $hall_id; ?>">
        <div class="form-group">
            <label for="hall_name">Hall Name:</label>
            <input type="text" class="form-control" id="hall_name" name="hall_name" value="<?php echo $hall_name; ?>" required>
        </div>
        <div class="form-group">
            <label for="capacity">Capacity:</label>
            <input type="number" class="form-control" id="capacity" name="capacity" value="<?php echo $capacity; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Hall</button>
        <a class="btn btn-outline-dark" href="view_halls.php">Cancel</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
