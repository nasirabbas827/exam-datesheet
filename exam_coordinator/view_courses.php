<?php
session_start();
include('config.php');

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Handle delete course request
if (isset($_GET['delete'])) {
    $course_id = $_GET['delete'];

    // Delete course
    $delete_course_sql = "DELETE FROM Courses WHERE course_id = ?";
    $delete_course_stmt = $conn->prepare($delete_course_sql);
    $delete_course_stmt->bind_param("i", $course_id);
    
    // Delete enrollments for the course
    $delete_enrollments_sql = "DELETE FROM Enrollments WHERE course_id = ?";
    $delete_enrollments_stmt = $conn->prepare($delete_enrollments_sql);
    $delete_enrollments_stmt->bind_param("i", $course_id);

    // Execute deletion
    $conn->autocommit(FALSE); // Start transaction
    $delete_success = true;

    // Delete course
    if (!$delete_course_stmt->execute()) {
        $delete_success = false;
    }

    // Delete enrollments
    if (!$delete_enrollments_stmt->execute()) {
        $delete_success = false;
    }

    // Commit or rollback transaction
    if ($delete_success) {
        $conn->commit();
        echo "<script>alert('Course and associated enrollments deleted successfully'); window.location.href = 'view_courses.php';</script>";
    } else {
        $conn->rollback();
        echo "<script>alert('Error deleting course and enrollments'); window.location.href = 'view_courses.php';</script>";
    }

    // Close statements
    $delete_course_stmt->close();
    $delete_enrollments_stmt->close();
}

// Handle search request
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Function to fetch courses with search functionality
function fetchCourses($conn, $search_query) {
    $sql = "SELECT c.course_id, c.course_code, c.course_name, f.name AS faculty_name
            FROM Courses c
            LEFT JOIN Faculty f ON c.faculty_id = f.id
            WHERE c.course_name LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_query = '%' . $search_query . '%';
    $stmt->bind_param("s", $search_query);
    $stmt->execute();
    return $stmt->get_result();
}

// Fetch all courses with search functionality
$result = fetchCourses($conn, $search_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Courses</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>All Courses</h2>

    <!-- Search Form -->
    <form method="get" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by course name" value="<?php echo htmlspecialchars($search_query); ?>">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </div>
    </form>

    <a href="add_course.php" class="btn btn-success mb-3 float-right">Add New Course</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Course Code</th>
                <th>Course Name</th>
                <th>Faculty</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['course_id']}</td>
                            <td>{$row['course_code']}</td>
                            <td>{$row['course_name']}</td>
                            <td>{$row['faculty_name']}</td>
                            <td>
                                <a href='edit_course.php?course_id={$row['course_id']}' class='btn btn-primary btn-sm'>Edit</a>
                                <a href='view_courses.php?delete={$row['course_id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this course and its enrollments?');\">Delete</a>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No courses found</td></tr>";
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
