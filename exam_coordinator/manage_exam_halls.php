<?php
session_start();
include('config.php');

// Check if the user is an exam coordinator
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Initialize form data variables
$hall_id = '';
$building = '';
$floor = '';
$hall_number = '';
$seating_capacity = '';

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add or update an exam hall
    if (isset($_POST['add_hall']) || isset($_POST['update_hall'])) {
        $building = $_POST['building'];
        $floor = $_POST['floor'];
        $hall_number = $_POST['hall_number'];
        $seating_capacity = $_POST['seating_capacity'];

        if (isset($_POST['hall_id']) && !empty($_POST['hall_id'])) {
            // Update existing exam hall
            $hall_id = $_POST['hall_id'];
            $stmt = $conn->prepare("UPDATE exam_halls SET building=?, floor=?, hall_number=?, seating_capacity=? WHERE id=?");
            $stmt->bind_param("sisii", $building, $floor, $hall_number, $seating_capacity, $hall_id);
            $stmt->execute();
            echo "<div class='alert alert-success'>Exam hall updated successfully!</div>";
        } else {
            // Insert new exam hall
            $stmt = $conn->prepare("INSERT INTO exam_halls (building, floor, hall_number, seating_capacity) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sisi", $building, $floor, $hall_number, $seating_capacity);
            $stmt->execute();
            echo "<div class='alert alert-success'>Exam hall added successfully!</div>";
        }
    }

    // Handle delete exam hall
    if (isset($_POST['delete_hall'])) {
        $hall_id = $_POST['hall_id'];
        $stmt = $conn->prepare("DELETE FROM exam_halls WHERE id = ?");
        $stmt->bind_param("i", $hall_id);
        $stmt->execute();
        echo "<div class='alert alert-success'>Exam hall deleted successfully!</div>";
    }

    // Handle edit exam hall - fetch the data to populate the form
    if (isset($_POST['edit_hall'])) {
        $hall_id = $_POST['hall_id'];
        $stmt = $conn->prepare("SELECT * FROM exam_halls WHERE id = ?");
        $stmt->bind_param("i", $hall_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $hall = $result->fetch_assoc();

        $building = $hall['building'];
        $floor = $hall['floor'];
        $hall_number = $hall['hall_number'];
        $seating_capacity = $hall['seating_capacity'];
    }
}

// Fetch all exam halls to display in the table
$exam_halls = $conn->query("SELECT * FROM exam_halls");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Exam Halls</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Manage Exam Halls</h2>

    <!-- Add or Update Exam Hall Form -->
    <form action="manage_exam_halls.php" method="post">
        <div class="form-group">
            <label for="building">Building:</label>
            <input type="text" class="form-control" id="building" name="building" value="<?php echo $building; ?>" required>
        </div>
        <div class="form-group">
            <label for="floor">Floor:</label>
            <input type="number" class="form-control" id="floor" name="floor" value="<?php echo $floor; ?>" required>
        </div>
        <div class="form-group">
            <label for="hall_number">Hall Number:</label>
            <input type="text" class="form-control" id="hall_number" name="hall_number" value="<?php echo $hall_number; ?>" required>
        </div>
        <div class="form-group">
            <label for="seating_capacity">Seating Capacity:</label>
            <input type="number" class="form-control" id="seating_capacity" name="seating_capacity" value="<?php echo $seating_capacity; ?>" required>
        </div>
        <?php if (!empty($hall_id)): ?>
            <input type="hidden" name="hall_id" value="<?php echo $hall_id; ?>">
            <button type="submit" name="update_hall" class="btn btn-primary">Update Exam Hall</button>
        <?php else: ?>
            <button type="submit" name="add_hall" class="btn btn-primary">Add Exam Hall</button>
        <?php endif; ?>
    </form>

    <!-- List of all exam halls -->
    <h4 class="mt-5">All Exam Halls</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Building</th>
                <th>Floor</th>
                <th>Hall Number</th>
                <th>Seating Capacity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($hall = $exam_halls->fetch_assoc()): ?>
            <tr>
                <td><?php echo $hall['id']; ?></td>
                <td><?php echo $hall['building']; ?></td>
                <td><?php echo $hall['floor']; ?></td>
                <td><?php echo $hall['hall_number']; ?></td>
                <td><?php echo $hall['seating_capacity']; ?></td>
                <td>
                    <!-- Update Exam Hall Form -->
                    <form action="manage_exam_halls.php" method="post" style="display:inline-block;">
                        <input type="hidden" name="hall_id" value="<?php echo $hall['id']; ?>">
                        <button type="submit" name="edit_hall" class="btn btn-info">Edit</button>
                    </form>

                    <!-- Delete Exam Hall Form -->
                    <form action="manage_exam_halls.php" method="post" style="display:inline-block;">
                        <input type="hidden" name="hall_id" value="<?php echo $hall['id']; ?>">
                        <button type="submit" name="delete_hall" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this hall?');">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
