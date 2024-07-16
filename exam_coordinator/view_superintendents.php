<?php
session_start();
include('config.php');

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Function to fetch all superintendents
function fetchSuperintendents($conn) {
    $sql = "SELECT s.id, f.name AS faculty_name, s.email, s.password 
            FROM Superintendents s
            INNER JOIN Faculty f ON s.faculty_id = f.id";
    $result = $conn->query($sql);
    return $result;
}

// Function to handle delete superintendent request
function deleteSuperintendent($conn, $superintendent_id) {
    $sql = "DELETE FROM Superintendents WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $superintendent_id);
    $stmt->execute();
    $stmt->close();
}

// Handle delete request
if (isset($_GET['delete'])) {
    $superintendent_id = $_GET['delete'];
    deleteSuperintendent($conn, $superintendent_id);
    header("Location: view_superintendents.php");
    exit;
}

// Fetch all superintendents
$result = fetchSuperintendents($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Superintendents</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>View Superintendents</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Faculty Name</th>
                <th>Email</th>
                <th>Password</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['id']}</td>";
                    echo "<td>{$row['faculty_name']}</td>";
                    echo "<td>{$row['email']}</td>";
                    echo "<td>{$row['password']}</td>";
                    echo "<td>
                            <a href='edit_superintendent.php?id={$row['id']}' class='btn btn-primary btn-sm'>Edit</a>
                            <a href='view_superintendents.php?delete={$row['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this superintendent?');\">Delete</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No superintendents found</td></tr>";
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
