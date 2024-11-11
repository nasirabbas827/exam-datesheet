<?php
session_start();
include('config.php');

// Check if the user is logged in and has the right role
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Fetch courses
$courses = $conn->query("SELECT * FROM courses");

// Fetch superintendents
$superintendents = $conn->query("SELECT * FROM superintendents");

// Fetch exam halls
$exam_halls = $conn->query("SELECT * FROM exam_halls");

// Define time slots for each day
$slots = [
    'Slot 1' => '8:00 - 9:30',
    'Slot 2' => '10:00 - 11:30',
    'Slot 3' => '12:00 - 1:30',
    'Slot 4' => '2:00 - 3:30',
    'Slot 5' => '4:00 - 5:30'
];

// Display available slots as a paragraph
$slot_paragraph = "<p>Available Slots per Day:</p><ul>";
foreach ($slots as $slot_key => $slot_time) {
    $slot_paragraph .= "<li>$slot_key: $slot_time</li>";
}
$slot_paragraph .= "</ul>";

// Prepare an array to hold non-overlapping courses
$non_overlapping_courses = [];

// Function to fetch non-overlapping courses for a given course
function getNonOverlappingCourses($course_id, $conn) {
    $sql = "SELECT course_code
            FROM courses
            WHERE id != ?
            AND NOT EXISTS (
                SELECT 1 FROM enrollments e1
                JOIN enrollments e2 ON e1.student_id = e2.student_id
                WHERE e1.course_id = ? AND e2.course_id = courses.id
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

// Calculate non-overlapping courses for each course
while ($course = $courses->fetch_assoc()) {
    $course_id = $course['id'];
    $course_code = $course['course_code'];

    // Get non-overlapping courses using the function
    $non_overlaps = getNonOverlappingCourses($course_id, $conn);
    $non_overlapping_courses[$course_code] = $non_overlaps;
}

// Prepare eligible superintendents for each course
$eligible_superintendents = [];
$courses->data_seek(0); // Reset pointer to the beginning of courses
while ($course = $courses->fetch_assoc()) {
    $course_id = $course['id'];
    $course_code = $course['course_code'];

    // Get eligible superintendents
    $eligible = $conn->query("
        SELECT s.* FROM superintendents s
        WHERE NOT EXISTS (
            SELECT * FROM superintendent_courses sc
            WHERE sc.superintendent_id = s.id AND sc.course_id = $course_id
        )
    ");

    $eligible_list = [];
    while ($superintendent = $eligible->fetch_assoc()) {
        $eligible_list[] = $superintendent['name'];
    }
    $eligible_superintendents[$course_code] = $eligible_list;
}

// Prepare feasible exam halls for each course
$feasible_exam_halls = [];
$courses->data_seek(0); // Reset pointer to the beginning of courses
while ($course = $courses->fetch_assoc()) {
    $course_id = $course['id'];
    $course_code = $course['course_code'];

    // Get enrollments count
    $enrollment_count = $conn->query("SELECT COUNT(*) as count FROM enrollments WHERE course_id = $course_id")->fetch_assoc()['count'];

    // Get feasible exam halls
    $halls = $conn->query("SELECT * FROM exam_halls WHERE seating_capacity >= $enrollment_count");
    $halls_list = [];
    while ($hall = $halls->fetch_assoc()) {
        $halls_list[] = $hall['hall_number'];
    }
    $feasible_exam_halls[$course_code] = $halls_list;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Exam Schedule</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Manage Exam Schedule</h2>

        <?= $slot_paragraph; ?>

        <form method="POST" action="schedule.php" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="num_slots">Number of Slots Per Day (1-5):</label>
                <select id="num_slots" name="num_slots" class="form-control" required>
                    <option value="">Select number of slots</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i; ?>"><?= $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="min_days_between_exams">Minimum Number of Days Between Exams:</label>
                <input type="number" id="min_days_between_exams" name="min_days_between_exams" class="form-control"
                    min="0" required>
            </div>

            <button type="submit" class="btn btn-primary">Generate Schedule</button>
        </form>

        <script>
            function validateForm() {
                const numSlots = parseInt(document.getElementById('num_slots').value, 10);
                const minDaysBetweenExams = parseInt(document.getElementById('min_days_between_exams').value, 10);

                if (numSlots < 1 || numSlots > 5) {
                    alert("Please select a valid number of slots (between 1 and 5).");
                    return false;
                }

                if (minDaysBetweenExams < 0) {
                    alert("Minimum number of days between exams cannot be negative.");
                    return false;
                }

                return true;
            }
        </script>

        <h4 class="mt-5">Non-overlapping Courses</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>List of Non-overlapping Courses</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($non_overlapping_courses as $course_code => $non_overlaps): ?>
                <tr>
                    <td>
                        <?php echo $course_code; ?>
                    </td>
                    <td>
                        <?php echo empty($non_overlaps) ? "None" : implode(", ", $non_overlaps); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h4 class="mt-5">Eligible Superintendents for Courses</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Eligible Superintendents</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($eligible_superintendents as $course_code => $superintendents): ?>
                <tr>
                    <td>
                        <?php echo $course_code; ?>
                    </td>
                    <td>
                        <?php echo implode(", ", $superintendents); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h4 class="mt-5">Feasible Exam Halls for Courses</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Feasible Exam Halls</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feasible_exam_halls as $course_code => $halls): ?>
                <tr>
                    <td>
                        <?php echo $course_code; ?>
                    </td>
                    <td>
                        <?php echo implode(", ", $halls); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
