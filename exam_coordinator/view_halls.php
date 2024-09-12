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

// Handle search request
$search_query = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';

// Fetch all examination halls with search functionality
$sql = "SELECT * FROM ExaminationHalls WHERE hall_name LIKE ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $search_query);
$stmt->execute();
$result = $stmt->get_result();
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

    <!-- Search Form -->
    <form method="get" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by hall name" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </div>
    </form>

    <a href="add_hall.php" class="btn btn-success mb-3 float-right">Add New Hall</a>
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
