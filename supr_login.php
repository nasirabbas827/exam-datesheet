<?php
session_start();
include('config.php');

// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // If no errors, check credentials and log in user
    if (empty($email_err) && empty($password_err)) {
        $sql = "SELECT id, email, password FROM Superintendents WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $param_email);
        $param_email = $email;
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $email, $db_password);
            if ($stmt->fetch()) {
                if ($password === $db_password) {  // Directly compare plaintext passwords
                    // Start session and log in user
                    $_SESSION["id"] = $id;
                    $_SESSION["email"] = $email;
                    header("location: superintendent/superintendent_dashboard.php");
                } else {
                    $password_err = "The password you entered was not valid.";
                }
            }
        } else {
            // Email not found in database
            $email_err = "No account found with that email.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superintendent Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Superintendent Login</h2>
    <p class="text-center">Please fill in your credentials to log in.</p>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label>Email</label>
            <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
            <span class="text-danger"><?php echo $email_err; ?></span>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
            <span class="text-danger"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group text-center">
            <input type="submit" value="Log in" class="btn btn-primary">
        </div>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
