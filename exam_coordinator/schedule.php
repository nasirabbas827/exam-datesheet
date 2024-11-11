<?php
session_start();
include('config.php');

// Check if the user is logged in and has the right role
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Input values for slots per day and minimum days between exams
$slots_per_day = $_POST['num_slots'] ?? 5;
$min_days_between_exams = $_POST['min_days_between_exams'] ?? 2;

// Define time slots
$time_slots = [
    'Slot 1' => '8:00 - 9:30',
    'Slot 2' => '10:00 - 11:30',
    'Slot 3' => '12:00 - 1:30',
    'Slot 4' => '2:00 - 3:30',
    'Slot 5' => '4:00 - 5:30'
];
$time_slots = array_slice($time_slots, 0, $slots_per_day);

// Fetch enrollment data
$enrollments = $conn->query("SELECT course_id, student_id FROM enrollments");
$student_courses = [];
while ($enrollment = $enrollments->fetch_assoc()) {
    $student_courses[$enrollment['student_id']][] = $enrollment['course_id'];
}

// Identify students enrolled in multiple courses
$multi_enrolled_students = [];
foreach ($student_courses as $student_id => $courses) {
    if (count($courses) > 1) {
        $multi_enrolled_students[$student_id] = $courses;
    }
}

// Identify overlapping courses
$overlapping_courses = [];
foreach ($multi_enrolled_students as $courses) {
    foreach ($courses as $i => $course1) {
        for ($j = $i + 1; $j < count($courses); $j++) {
            $course2 = $courses[$j];
            $overlapping_courses[$course1][] = $course2;
            $overlapping_courses[$course2][] = $course1;
        }
    }
}
foreach ($overlapping_courses as $course => $overlaps) {
    $overlapping_courses[$course] = array_unique($overlaps);
}

// Schedule exams
$schedule = [];
$scheduled_courses = [];

function schedule_course($course_id, &$schedule, &$scheduled_courses, $min_days_between_exams, $time_slots, &$overlapping_courses) {
    global $conn;

    for ($day = 1; $day <= 100; $day++) {
        foreach ($time_slots as $slot => $time_range) {
            $valid = true;

            // Check if the slot is available
            foreach ($schedule as $entry) {
                if ($entry['day'] === $day && $entry['slot'] === $slot) {
                    $valid = false;
                    break;
                }
            }

            // Check overlapping course constraints
            foreach ($overlapping_courses[$course_id] ?? [] as $overlapping_course) {
                if (isset($scheduled_courses[$overlapping_course])) {
                    $overlap_day = $scheduled_courses[$overlapping_course]['day'];
                    $day_gap = abs($day - $overlap_day);
                    if ($day_gap < $min_days_between_exams) {
                        $valid = false;
                        break;
                    }
                }
            }

            if ($valid) {
                $schedule[] = [
                    'course_id' => $course_id,
                    'day' => $day,
                    'slot' => $slot,
                    'time_range' => $time_range
                ];
                $scheduled_courses[$course_id] = ['day' => $day, 'slot' => $slot];
                return;
            }
        }
    }
}

// Schedule courses
$courses = $conn->query("SELECT id, course_code FROM courses");
while ($course = $courses->fetch_assoc()) {
    schedule_course($course['id'], $schedule, $scheduled_courses, $min_days_between_exams, $time_slots, $overlapping_courses);
}

// Assign superintendents and halls
$superintendent_assignments = [];
$hall_assignments = [];

foreach ($schedule as $index => $entry) {
    $course_id = $entry['course_id'];

    // Assign superintendents
    $superintendents = $conn->query("
        SELECT s.id, s.name FROM superintendents s
        WHERE NOT EXISTS (
            SELECT 1 FROM superintendent_courses sc WHERE sc.superintendent_id = s.id AND sc.course_id = $course_id
        )
    ");
    $superintendent = $superintendents->fetch_assoc();
    $superintendent_assignments[$course_id] = $superintendent['name'] ?? 'N/A';
    $superintendent_ids[$course_id] = $superintendent['id'] ?? NULL;

    // Assign halls
    $enrollment_count = $conn->query("SELECT COUNT(*) as count FROM enrollments WHERE course_id = $course_id")->fetch_assoc()['count'];
    $halls = $conn->query("
        SELECT hall_number FROM exam_halls WHERE seating_capacity >= $enrollment_count
    ");
    $hall = $halls->fetch_assoc();
    $hall_assignments[$course_id] = $hall['hall_number'] ?? 'N/A';
}

// Store schedule data in session
$_SESSION['schedule_data'] = array_map(function($entry) use ($superintendent_assignments, $hall_assignments, $superintendent_ids) {
    return [
        'course_id' => $entry['course_id'],
        'day' => $entry['day'],
        'slot' => $entry['slot'],
        'time_range' => $entry['time_range'],
        'superintendent_id' => $superintendent_ids[$entry['course_id']],
        'hall_number' => $hall_assignments[$entry['course_id']]
    ];
}, $schedule);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Exam Schedule</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <?php include('navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2>Exam Schedule</h2>

    <h4>Students Enrolled in Multiple Courses</h4>
    <ul>
        <?php foreach ($multi_enrolled_students as $student_id => $courses): ?>
            <li>Student ID: <?= $student_id ?>, Courses: <?= implode(", ", $courses) ?></li>
        <?php endforeach; ?>
    </ul>

    <?php if ($schedule): ?>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Course Code</th>
                <th>Day</th>
                <th>Slot</th>
                <th>Time</th>
                <th>Superintendent</th>
                <th>Exam Hall</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($schedule as $entry): ?>
                <?php
                $course_code = $conn->query("SELECT course_code FROM courses WHERE id = {$entry['course_id']}")->fetch_assoc()['course_code'];
                ?>
                <tr>
                    <td><?= $course_code ?></td>
                    <td>Day <?= $entry['day'] ?></td>
                    <td><?= $entry['slot'] ?></td>
                    <td><?= $entry['time_range'] ?></td>
                    <td><?= $superintendent_assignments[$entry['course_id']] ?></td>
                    <td><?= $hall_assignments[$entry['course_id']] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <form method="post" action="save_schedule.php">
            <button type="submit" class="btn btn-primary float-right mb-5">Save Schedule</button>
        </form>
    <?php else: ?>
        <p>No schedule could be generated.</p>
    <?php endif; ?>
</div>
</body>

</html>
