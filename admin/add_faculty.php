<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Function to handle form submission and add a new faculty member
function handleFormSubmission($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST["name"];
        
        // Check for existing faculty name
        $sql = "SELECT * FROM Faculty WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Faculty name already exists'); window.location.href = '".$_SERVER['PHP_SELF']."';</script>";
        } else {
            // Insert the new faculty member
            $sql = "INSERT INTO Faculty (name) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $name);

            if ($stmt->execute()) {
                echo "<script>alert('Faculty added successfully'); window.location.href = '".$_SERVER['PHP_SELF']."';</script>";
            } else {
                echo "<script>alert('Error adding faculty'); window.location.href = '".$_SERVER['PHP_SELF']."';</script>";
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
    <title>Admin Dashboard - Add Faculty</title>
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
    <h2>Add New Faculty</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Faculty</button>
        <a class="btn btn-outline-dark" href="view_faculty.php">View Faculty</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
