<?php
session_start();
include('config.php');

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $faculty_id = $_POST['faculty_id'];
    $email = $_POST['email'];
    $password = $_POST['password']; 

    // Check if faculty already assigned as superintendent
    $check_sql = "SELECT * FROM Superintendents WHERE faculty_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $faculty_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('This faculty member is already assigned as a superintendent'); window.location.href = '".$_SERVER['PHP_SELF']."';</script>";
    } else {
        // Check if email already exists
        $email_check_sql = "SELECT * FROM Superintendents WHERE email = ?";
        $email_check_stmt = $conn->prepare($email_check_sql);
        $email_check_stmt->bind_param("s", $email);
        $email_check_stmt->execute();
        $email_check_result = $email_check_stmt->get_result();

        if ($email_check_result->num_rows > 0) {
            echo "<script>alert('This email is already in use by another superintendent'); window.location.href = '".$_SERVER['PHP_SELF']."';</script>";
        } else {
            // Insert the new superintendent
            $insert_sql = "INSERT INTO Superintendents (faculty_id, email, password) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("iss", $faculty_id, $email, $password);

            if ($insert_stmt->execute()) {
                echo "<script>alert('Superintendent added successfully'); window.location.href = 'view_superintendents.php';</script>";
            } else {
                echo "<script>alert('Error adding superintendent'); window.location.href = '".$_SERVER['PHP_SELF']."';</script>";
            }

            $insert_stmt->close();
        }

        $email_check_stmt->close();
    }

    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Superintendent</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2>Add New Superintendent</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <div class="form-group">
            <label for="faculty_id">Select Faculty Member:</label>
            <select class="form-control" id="faculty_id" name="faculty_id" required>
                <?php
                // Fetch faculty members who are not already superintendents
                $sql_faculty = "SELECT id, name FROM Faculty WHERE id NOT IN (SELECT faculty_id FROM Superintendents)";
                $result_faculty = $conn->query($sql_faculty);
                while ($row = $result_faculty->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Superintendent</button>
        <a class="btn btn-outline-dark" href="view_superintendents.php">View Superintendents</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
