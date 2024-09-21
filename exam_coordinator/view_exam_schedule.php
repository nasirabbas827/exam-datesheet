<?php
session_start();
include 'config.php';

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Function to fetch non-overlapping courses for a given course
function getNonOverlappingCourses($course_id, $conn) {
    $sql = "SELECT c.course_code
            FROM Courses c
            WHERE c.course_id != ?
            AND NOT EXISTS (
                SELECT 1 
                FROM Enrollments e1
                JOIN Enrollments e2 ON e1.student_id = e2.student_id
                WHERE e1.course_id = ? AND e2.course_id = c.course_id
            )";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $course_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $non_overlapping_courses = [];
    while ($row = $result->fetch_assoc()) {
        $non_overlapping_courses[] = $row['course_code'];
    }

    $stmt->close();
    return implode(', ', $non_overlapping_courses);
}


// Function to fetch superintendents and their associated courses
function getSuperintendentCourses($conn) {
    $sql = "SELECT s.id, f.name as faculty_name, c.course_code
            FROM superintendents s
            JOIN Faculty f ON s.faculty_id = f.id
            LEFT JOIN Courses c ON c.faculty_id = f.id";
    $result = $conn->query($sql);

    $superintendents = [];
    while ($row = $result->fetch_assoc()) {
        if (!isset($superintendents[$row['id']])) {
            $superintendents[$row['id']]['faculty_name'] = $row['faculty_name'];
            $superintendents[$row['id']]['courses'] = [];
        }
        if ($row['course_code']) {
            $superintendents[$row['id']]['courses'][] = $row['course_code'];
        }
    }

    return $superintendents;
}


// Fetch all courses
$sql_courses = "SELECT course_id, course_code FROM Courses";
$result_courses = $conn->query($sql_courses);

// Fetch superintendents and their courses
$superintendents = getSuperintendentCourses($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Exam Schedule</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2>Non-overlapping Courses</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>List of Non-overlapping Courses</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result_courses->fetch_assoc()) {
                    $course_id = $row['course_id'];
                    $course_code = $row['course_code'];
                    $non_overlapping_courses = getNonOverlappingCourses($course_id, $conn);
                    
                    echo "<tr>";
                    echo "<td>{$course_code}</td>";
                    echo "<td>{$non_overlapping_courses}</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <h2>Superintendents and Their Courses</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Superintendent Name</th>
                    <th>Courses Taught</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($superintendents as $superintendent_id => $superintendent) {
                    echo "<tr>";
                    echo "<td>{$superintendent['faculty_name']}</td>";
                    echo "<td>" . implode(', ', $superintendent['courses']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="container mt-3">
        <h1>Calculate Exam Schedule</h1>

        <form action="calculate_schedule.php" method="POST">
            <div class="form-group">
                <label for="slots_per_day">Number of Slots per Day:</label>
                <input type="number" id="slots_per_day" name="slots_per_day" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Calculate Exam Schedule</button>
        </form>

    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
