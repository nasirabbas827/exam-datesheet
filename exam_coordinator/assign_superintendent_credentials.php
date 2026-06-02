<?php
session_start();
include('config.php');

// Check if the user is logged in and has the right role
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "exam_coordinator") {
    header("Location: login.php");
    exit;
}

// Handle form submissions
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_credentials'])) {
        $id = $_POST['id'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        // Check if email is unique
        $email_check = $conn->query("SELECT * FROM superintendents WHERE email = '$email'");
        if ($email_check->num_rows > 0) {
            $message = "Email already exists. Please use a different email.";
        } else {
            $conn->query("UPDATE superintendents SET email = '$email', password = "YOUR_OWN_API_KEY" WHERE id = $id");
            $message = "Credentials assigned successfully!";
        }
    } elseif (isset($_POST['edit_credentials'])) {
        $id = $_POST['id'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        // Check if email is unique for other users
        $email_check = $conn->query("SELECT * FROM superintendents WHERE email = '$email' AND id != $id");
        if ($email_check->num_rows > 0) {
            $message = "Email already exists. Please use a different email.";
        } else {
            $conn->query("UPDATE superintendents SET email = '$email', password = "YOUR_OWN_API_KEY" WHERE id = $id");
            $message = "Credentials updated successfully!";
        }
    } elseif (isset($_POST['delete_superintendent'])) {
        $id = $_POST['id'];
        $conn->query("DELETE FROM superintendents WHERE id = $id");
        $message = "Superintendent deleted successfully!";
    }
}

// Fetch superintendents
$superintendents = $conn->query("
    SELECT id, name, designation, department, email 
    FROM superintendents
");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Superintendent Credentials</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>

<?php include('navbar.php'); ?>
<div class="container mt-5">
    <h2>Manage Superintendent Credentials</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Designation</th>
            <th>Department</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $superintendents->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['designation'] ?></td>
                <td><?= $row['department'] ?></td>
                <td><?= $row['email'] ?? 'Not Assigned' ?></td>
                <td>
                    <?php if (empty($row['email'])): ?>
                        <!-- Assign Credentials Form -->
                        <form method="post" class="d-inline">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="email" name="email" placeholder="Email" required>
                            <input type="password" name="password" placeholder="Password" required>
                            <button type="submit" name="add_credentials" class="btn btn-success btn-sm">Assign</button>
                        </form>
                    <?php else: ?>
                        <!-- Edit Credentials Form -->
                        <form method="post" class="d-inline">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="email" name="email" value="<?= $row['email'] ?>" required>
                            <input type="password" name="password" placeholder="New Password" required>
                            <button type="submit" name="edit_credentials" class="btn btn-warning btn-sm">Edit</button>
                        </form>

                        <!-- Delete Superintendent Form -->
                        <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this superintendent?');">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit" name="delete_superintendent" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>

</html>
