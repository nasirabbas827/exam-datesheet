<?php
session_start();
include 'config.php';

// Check if user is logged in as exam coordinator
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Function to fetch non-overlapping courses for a given course
function getNonOverlappingCourses($course_id, $conn) {
    $sql = "SELECT course_code
            FROM Courses
            WHERE course_id != ?
            AND NOT EXISTS (
                SELECT 1 FROM Enrollments e1
                JOIN Enrollments e2 ON e1.student_id = e2.student_id
                WHERE e1.course_id = ? AND e2.course_id = Courses.course_id
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
    return $non_overlapping_courses;
}

// Function to fetch superintendents and their associated courses
function getSuperintendentCourses($conn) {
    $sql = "SELECT s.id, f.name as faculty_name, c.course_code
            FROM superintendents s
            JOIN Faculty f ON s.faculty_id = f.id
            LEFT JOIN Courses c ON c.faculty_id = f.id"; // LEFT JOIN allows fetching faculty without courses
    $result = $conn->query($sql);

    $superintendents = [];
    while ($row = $result->fetch_assoc()) {
        if (!isset($superintendents[$row['id']])) {
            $superintendents[$row['id']]['faculty_name'] = $row['faculty_name'];
            $superintendents[$row['id']]['courses'] = []; // Initialize empty array for courses
        }
        if ($row['course_code']) {
            $superintendents[$row['id']]['courses'][] = $row['course_code']; // Store taught courses
        }
    }

    return $superintendents;
}

// Function to fetch exam halls with sufficient capacity for each course
function getExamHalls($conn) {
    $sql = "SELECT hall_id, hall_name, capacity
            FROM ExaminationHalls";
    $result = $conn->query($sql);
    
    $halls = [];
    while ($row = $result->fetch_assoc()) {
        $halls[$row['hall_id']]['hall_name'] = $row['hall_name'];
        $halls[$row['hall_id']]['capacity'] = $row['capacity'];
    }
    
    return $halls;
}

// Function to calculate exam schedule
function calculateExamSchedule($slots_per_day, $non_overlapping_courses, $superintendents, $halls, $conn) {
    // Initialize variables
    $schedule = [];
    $used_courses = [];
    $day_count = 0;
    $day_courses = [];

    // Group courses by non-overlapping constraints
    foreach ($non_overlapping_courses as $course_id => $non_overlap) {
        if (in_array($course_id, $used_courses)) {
            continue; // Skip if already scheduled
        }

        // Start a new day if needed
        if (!isset($day_courses[$day_count])) {
            $day_courses[$day_count] = [];
        }

        $can_schedule = true;
        foreach ($day_courses[$day_count] as $scheduled_course_id) {
            if (!in_array($scheduled_course_id, $non_overlapping_courses[$course_id])) {
                $can_schedule = false;
                break;
            }
        }

        if ($can_schedule) {
            $day_courses[$day_count][] = $course_id;
            $used_courses[] = $course_id;
        } else {
            $day_count++;
            $day_courses[$day_count] = [$course_id];
            $used_courses[] = $course_id;
        }
    }

    // Assign each day's courses to a superintendent and an exam hall
    foreach ($day_courses as $day => $courses) {
        $assigned_superintendent = "No superintendent assigned";
        $assigned_hall = "No suitable hall found";

        // Filter superintendents who didn't teach any of the day's courses
        $available_superintendents = [];
        foreach ($superintendents as $superintendent_id => $superintendent) {
            $teaches_overlap = false;
            foreach ($courses as $course_id) {
                if (in_array($course_id, $superintendent['courses'])) {
                    $teaches_overlap = true; // This superintendent taught this course
                    break;
                }
            }
            if (!$teaches_overlap) {
                $available_superintendents[$superintendent_id] = $superintendent;
            }
        }

        // Assign a superintendent if available
        if (!empty($available_superintendents)) {
            $random_superintendent = $available_superintendents[array_rand($available_superintendents)];
            $assigned_superintendent = $random_superintendent['faculty_name'];
        }

        // Assign a hall with sufficient capacity (greater than or equal to total enrollments for the day)
        foreach ($halls as $hall) {
            $total_enrollment = 0;
            foreach ($courses as $course_id) {
                $total_enrollment += getEnrollmentCount($course_id, $conn); // Pass $conn to the helper function
            }

            if ($hall['capacity'] >= $total_enrollment) {
                $assigned_hall = $hall['hall_name'];
                break;
            }
        }

        // Store the schedule for the current day
        $schedule[$day]['courses'] = $courses;
        $schedule[$day]['superintendent'] = $assigned_superintendent;
        $schedule[$day]['hall_name'] = $assigned_hall;
    }

    return $schedule;
}

// Helper function to get enrollment count for a specific course
function getEnrollmentCount($course_id, $conn) {
    $sql = "SELECT COUNT(student_id) as total_enrollment FROM Enrollments WHERE course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row['total_enrollment'];
}

// Initialize variables
$slots_per_day = isset($_POST['slots_per_day']) ? intval($_POST['slots_per_day']) : 0;
$non_overlapping_courses = [];
$superintendents = [];
$halls = [];
$schedule = [];
$schedule_exists = false;
$message = "";
$course_enrollments = [];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if a schedule already exists
    $sql_check_schedule = "SELECT COUNT(*) as count FROM ExamSchedule";
    $result_check_schedule = $conn->query($sql_check_schedule);
    $row_check_schedule = $result_check_schedule->fetch_assoc();
    if ($row_check_schedule['count'] > 0) {
        $schedule_exists = true;
        $message = "Exam schedule already exists. Please delete the existing schedule before generating a new one.";
    } else {
        // Fetch all courses with their enrollments
        $sql_courses = "SELECT Courses.course_id, Courses.course_code, COUNT(Enrollments.student_id) AS enrollment
                        FROM Courses
                        LEFT JOIN Enrollments ON Courses.course_id = Enrollments.course_id
                        GROUP BY Courses.course_id, Courses.course_code";
        $result_courses = $conn->query($sql_courses);

        // Fetch superintendents and their courses
        $superintendents = getSuperintendentCourses($conn);

        // Fetch exam halls
        $halls = getExamHalls($conn);

        // Array to hold the non-overlapping courses
        while ($row = $result_courses->fetch_assoc()) {
            $course_id = $row['course_id'];
            $course_code = $row['course_code'];
            $course_enrollments[$course_id] = $row['enrollment']; // Store the number of enrollments for each course
            $non_overlapping_courses[$course_id] = getNonOverlappingCourses($course_id, $conn);
        }

        // Calculate exam schedule
        $schedule = calculateExamSchedule($slots_per_day, $non_overlapping_courses, $superintendents, $halls, $conn);

        // Insert schedule into database
        foreach ($schedule as $day => $data) {
            $day_number = $day + 1;
            $courses = implode(', ', $data['courses']);
            $superintendent = $data['superintendent'];
            $hall_name = $data['hall_name'];

            $sql_insert_schedule = "INSERT INTO ExamSchedule (day_number, courses, superintendent, hall_name)
                                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql_insert_schedule);
            $stmt->bind_param("isss", $day_number, $courses, $superintendent, $hall_name);
            $stmt->execute();
            $stmt->close();
        }

        $message = "Exam schedule generated and saved to the database.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Exam Schedule</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2>Exam Schedule</h2>
    

    <?php if (!empty($message)): ?>
        <div class="alert alert-info mt-4">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !$schedule_exists && count($schedule) > 0): ?>
        <div class="table-responsive mt-4">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Courses</th>
                        <th>Superintendent</th>
                        <th>Exam Hall</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedule as $day => $data): ?>
                        <tr>
                            <td>Day <?php echo ($day + 1); ?></td>
                            <td><?php echo implode(', ', $data['courses']); ?></td>
                            <td><?php echo $data['superintendent']; ?></td>
                            <td><?php echo $data['hall_name']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

