<?php
session_start();
include('config.php');

// Check if the user is an exam coordinator
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Fetch the list of courses to be displayed in the form
$courses = $conn->query("SELECT * FROM courses");

// Initialize variables for form data
$superintendent_id = '';
$name = '';
$designation = '';
$department = '';
$selected_courses = [];

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add or update a superintendent record
    if (isset($_POST['add_superintendent']) || isset($_POST['update_superintendent'])) {
        $name = $_POST['name'];
        $designation = $_POST['designation'];
        $department = $_POST['department'];
        $selected_courses = $_POST['courses'];

        if (isset($_POST['superintendent_id']) && !empty($_POST['superintendent_id'])) {
            // Update existing superintendent
            $superintendent_id = $_POST['superintendent_id'];

            // Update superintendent record
            $stmt = $conn->prepare("UPDATE superintendents SET name=?, designation=?, department=? WHERE id=?");
            $stmt->bind_param("sssi", $name, $designation, $department, $superintendent_id);
            $stmt->execute();

            // Delete existing course assignments for this superintendent
            $stmt = $conn->prepare("DELETE FROM superintendent_courses WHERE superintendent_id = ?");
            $stmt->bind_param("i", $superintendent_id);
            $stmt->execute();
        } else {
            // Insert new superintendent record
            $stmt = $conn->prepare("INSERT INTO superintendents (name, designation, department) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $designation, $department);
            if ($stmt->execute()) {
                $superintendent_id = $conn->insert_id; // Get the ID of the newly inserted superintendent
            } else {
                echo "<div class='alert alert-danger'>Error adding superintendent: " . $stmt->error . "</div>";
            }
        }

        // Insert courses for the superintendent, ensuring no conflict with other superintendents
        $conflict = false;
        foreach ($selected_courses as $course_id) {
            // Check for conflict
            $stmt_check = $conn->prepare("SELECT * FROM superintendent_courses WHERE course_id = ?");
            $stmt_check->bind_param("i", $course_id);
            $stmt_check->execute();
            $result = $stmt_check->get_result();

            if ($result->num_rows > 0) {
                $conflict = true;
                echo "<div class='alert alert-danger'>Conflict: Course ID $course_id is already assigned to another superintendent.</div>";
                break;
            } else {
                // No conflict, assign the course to the superintendent
                $stmt_assign = $conn->prepare("INSERT INTO superintendent_courses (superintendent_id, course_id) VALUES (?, ?)");
                $stmt_assign->bind_param("ii", $superintendent_id, $course_id);
                $stmt_assign->execute();
            }
        }

        if (!$conflict) {
            echo "<div class='alert alert-success'>Superintendent record saved successfully!</div>";
        }
    }

    // Handle delete superintendent
    if (isset($_POST['delete_superintendent'])) {
        $superintendent_id = $_POST['superintendent_id'];
        $stmt = $conn->prepare("DELETE FROM superintendents WHERE id = ?");
        $stmt->bind_param("i", $superintendent_id);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Superintendent deleted successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error deleting superintendent: " . $stmt->error . "</div>";
        }
    }

    // Handle edit superintendent - fetch the data to populate the form
    if (isset($_POST['edit_superintendent'])) {
        $superintendent_id = $_POST['superintendent_id'];
        $stmt = $conn->prepare("SELECT * FROM superintendents WHERE id = ?");
        $stmt->bind_param("i", $superintendent_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $superintendent = $result->fetch_assoc();

        $name = $superintendent['name'];
        $designation = $superintendent['designation'];
        $department = $superintendent['department'];

        // Fetch the courses assigned to this superintendent
        $stmt_courses = $conn->prepare("SELECT course_id FROM superintendent_courses WHERE superintendent_id = ?");
        $stmt_courses->bind_param("i", $superintendent_id);
        $stmt_courses->execute();
        $result_courses = $stmt_courses->get_result();
        $selected_courses = [];
        while ($row = $result_courses->fetch_assoc()) {
            $selected_courses[] = $row['course_id'];
        }
    }
}

// Fetch all superintendents to display
$superintendents = $conn->query("SELECT s.id, s.name, s.designation, s.department FROM superintendents s");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Superintendents</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Manage Superintendents</h2>

    <!-- Add or Update Superintendent Form -->
    <form action="manage_superintendents.php" method="post">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" required>
        </div>
        <div class="form-group">
            <label for="designation">Designation:</label>
            <input type="text" class="form-control" id="designation" name="designation" value="<?php echo $designation; ?>" required>
        </div>
        <div class="form-group">
            <label for="department">Department:</label>
            <input type="text" class="form-control" id="department" name="department" value="<?php echo $department; ?>" required>
        </div>
        <div class="form-group">
            <label for="courses">Courses Taught:</label>
            <div class="form-check">
                <?php while ($course = $courses->fetch_assoc()): ?>
                    <input class="form-check-input" type="checkbox" name="courses[]" value="<?php echo $course['id']; ?>" id="course_<?php echo $course['id']; ?>" 
                        <?php echo in_array($course['id'], $selected_courses) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="course_<?php echo $course['id']; ?>">
                        <?php echo $course['course_code']; ?>
                    </label><br>
                <?php endwhile; ?>
            </div>
        </div>
        <?php if (!empty($superintendent_id)): ?>
            <input type="hidden" name="superintendent_id" value="<?php echo $superintendent_id; ?>">
            <button type="submit" name="update_superintendent" class="btn btn-primary">Update Superintendent</button>
        <?php else: ?>
            <button type="submit" name="add_superintendent" class="btn btn-primary">Add Superintendent</button>
        <?php endif; ?>
    </form>

    <!-- List of all superintendents -->
    <h4 class="mt-5">All Superintendents</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Department</th>
                <th>Courses Taught</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($superintendent = $superintendents->fetch_assoc()): ?>
            <tr>
                <td><?php echo $superintendent['id']; ?></td>
                <td><?php echo $superintendent['name']; ?></td>
                <td><?php echo $superintendent['designation']; ?></td>
                <td><?php echo $superintendent['department']; ?></td>
                <td>
                    <?php
                    $superintendent_id = $superintendent['id'];
                    $courses_taught = $conn->query("SELECT c.course_code FROM superintendent_courses sc JOIN courses c ON sc.course_id = c.id WHERE sc.superintendent_id = $superintendent_id");
                    while ($course = $courses_taught->fetch_assoc()) {
                        echo $course['course_code'] . " ";
                    }
                    ?>
                </td>
                <td>
                    <!-- Update Superintendent Form -->
                    <form action="manage_superintendents.php" method="post" style="display:inline-block;">
                        <input type="hidden" name="superintendent_id" value="<?php echo $superintendent['id']; ?>">
                        <button type="submit" name="edit_superintendent" class="btn btn-info">Edit</button>
                    </form>

                    <!-- Delete Superintendent Form -->
                    <form action="manage_superintendents.php" method="post" style="display:inline-block;">
                        <input type="hidden" name="superintendent_id" value="<?php echo $superintendent['id']; ?>">
                        <button type="submit" name="delete_superintendent" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this superintendent?');">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
