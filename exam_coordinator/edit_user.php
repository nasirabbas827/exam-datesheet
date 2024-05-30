<?php
session_start();
include('config.php');

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $password = !empty($_POST['password']) ? $_POST['password'] : null;
    $role = $_POST['role'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    if ($password) {
        $sql = "UPDATE Users SET username=?, password=?, role=?, name=?, email=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $username, $password, $role, $name, $email, $id);
    } else {
        $sql = "UPDATE Users SET username=?, role=?, name=?, email=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $username, $role, $name, $email, $id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('User updated successfully'); window.location.href = 'view_users.php';</script>";
    } else {
        echo "<script>alert('Error updating user'); window.location.href = 'edit_user.php?id={$id}';</script>";
    }
    $stmt->close();
}

// Fetch user details
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM Users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    header("Location: view_users.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
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
    <h2>Edit User</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo $user['username']; ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password (leave blank to keep the same):</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="form-group">
            <label for="role">Role:</label>
            <select class="form-control" id="role" name="role" required>
                <option value="student" <?php echo $user['role'] == 'student' ? 'selected' : ''; ?>>Student</option>
                <option value="superintendent" <?php echo $user['role'] == 'superintendent' ? 'selected' : ''; ?>>Superintendent</option>
                <option value="exam_coordinator" <?php echo $user['role'] == 'exam_coordinator' ? 'selected' : ''; ?>>Exam Coordinator</option>
            </select>
        </div>
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $user['name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update User</button>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
