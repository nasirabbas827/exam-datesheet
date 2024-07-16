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
    $id = intval($_POST["id"]);
    $name = $_POST["name"];

    $sql = "UPDATE Faculty SET name = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $name, $id);
    if ($stmt->execute()) {
        echo "<script>alert('Faculty member updated successfully'); window.location.href = 'view_faculty.php';</script>";
    } else {
        echo "<script>alert('Error updating faculty member'); window.location.href = 'edit_faculty.php?id={$id}';</script>";
    }
    $stmt->close();
}

// Fetch faculty member details
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM Faculty WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $faculty = $result->fetch_assoc();
    } else {
        echo "<script>alert('Faculty member not found'); window.location.href = 'view_faculty.php';</script>";
    }
    $stmt->close();
} else {
    echo "<script>alert('Invalid request'); window.location.href = 'view_faculty.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Faculty</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2>Edit Faculty Member</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <input type="hidden" name="id" value="<?php echo $faculty['id']; ?>">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $faculty['name']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a class="btn btn-outline-dark" href="view_faculty.php">Back</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
