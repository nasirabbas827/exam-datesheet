<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Function to handle form submission and add a new user
function handleFormSubmission($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $role = $_POST["role"];
        $name = $_POST["name"];
        $email = $_POST["email"];
        
        // Check for existing username or email
        $sql = "SELECT * FROM Users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Username or email already exists'); window.location.href = '".$_SERVER['PHP_SELF']."';</script>";
        } else {
            // Insert the new user
            $sql = "INSERT INTO Users (username, password, role, name, email) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $username, $password, $role, $name, $email);

            if ($stmt->execute()) {
                echo "<script>alert('User added successfully'); window.location.href = '".$_SERVER['PHP_SELF']."';</script>";
            } else {
                echo "<script>alert('Error adding user'); window.location.href = '".$_SERVER['PHP_SELF']."';</script>";
            }
        }
        
        $stmt->close();
    }
}

handleFormSubmission($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php
include('admin_navbar.php');
?>
<div class="container mt-5 mb-5">
    <h2>Add New User</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="role">Role:</label>
            <select class="form-control" id="role" name="role" required>
                <option value="student">Student</option>
                <option value="superintendent">Superintendent</option>
                <option value="exam_coordinator">Exam Coordinator</option>
            </select>
        </div>
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <button type="submit" class="btn btn-primary">Add User</button>
        <a class="btn btn-outline-dark" href="view_users.php">View Users</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
