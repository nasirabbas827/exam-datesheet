<?php
session_start();
include('config.php');

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $superintendent_id = $_POST['superintendent_id'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Remember to hash this in a real application

    // Update the superintendent details
    $sql = "UPDATE Superintendents SET email=?, password=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $email, $password, $superintendent_id);

    if ($stmt->execute()) {
        echo "<script>alert('Superintendent updated successfully'); window.location.href = 'view_superintendents.php';</script>";
    } else {
        echo "<script>alert('Error updating superintendent'); window.location.href = 'edit_superintendent.php?id={$superintendent_id}';</script>";
    }
    $stmt->close();
}

// Fetch superintendent details
if (isset($_GET['id'])) {
    $superintendent_id = $_GET['id'];
    $sql = "SELECT * FROM Superintendents WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $superintendent_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $superintendent = $result->fetch_assoc();
    $stmt->close();
} else {
    header("Location: view_superintendents.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Superintendent</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Edit Superintendent</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <input type="hidden" name="superintendent_id" value="<?php echo $superintendent['id']; ?>">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $superintendent['email']; ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" value="<?php echo $superintendent['password']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Superintendent</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
